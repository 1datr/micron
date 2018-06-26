<?php
// module loader and manager

class MLAM {

	var $_MODULES_OBJS=[];
	var $_SETTINGS=[];
	var $_MUST_SAVE=[];
	var $_LOADING_QUEUE=[];

	function load_modules()
	{
		// load modules settings
		
		
		$fp_settings  = new FilePair("./modules/settings.php");
		//
		$this->_SETTINGS = $fp_settings->get_settings();
		
		$modules = get_files_in_folder('modules',['dirs'=>true,'basename'=>true]);
		foreach ($modules as $mod)
		{							
				
			$mod_make_res = $this->load_module($mod);
		}
		// событие после загрузки
		foreach ($this->_MODULES_OBJS as $_modname => $mod_obj)
		{
			$this->_MODULES_OBJS[$_modname]->AfterLoad();
			//$this->_AfterLoad($_modname);
		}
	}
	
	function _AfterLoad($_modname)
	{
		if(!in_array($_modname,$this->_AFTERLOAD_EXECUTED))
		{
			$reqs = $this->_MODULES_OBJS[$_modname]->required();
			foreach ($reqs as $reqmod)
			{
				$this->_AfterLoad($reqmod);
			}
			$this->_MODULES_OBJS[$_modname]->AfterLoad();
			
			$this->_AFTERLOAD_EXECUTED[]=$modname;
		}
	}

	function get_mod_class_name($mod)
	{
		return "modules\\".str_replace('.', '\\', $mod)."\Module";
	}

	private function main_mod_file_name($mod)
	{
		return "./modules/$mod/".strtr($mod,'.','_').".php";
	}
	// создать объект модуля
	function load_module($mod)
	{
		try{
			
			if($this->module_loaded($mod)) // модуль уже загружен
				return true;
					
			if(!$this->module_enabled($mod)) // модуль недоступен
			{
				return false;
			}
			
			$_main_file = $this->main_mod_file_name($mod);
			if(file_exists($_main_file))
			{
				require_once $_main_file;
			}
			else 
				return false;			
					
			$mod_class = $this->get_mod_class_name($mod);
			
			$mod_settings = $mod_class::settings();
			if($mod_settings['sess_save'])
			{
				$this->_MUST_SAVE[]=$mod;
				
				$mod_obj = $this->unserialize($mod);
				if($mod_obj===null)
				{
					$mod_obj = new $mod_class(['path'=> "".dirname($_main_file)]);
					$mod_obj->onload_basic();
				}
			}
			else
			{
				$mod_obj = new $mod_class(['path'=> "".dirname($_main_file)]);
			}
			$mod_obj->_MOD_NAME = $mod;
			$mod_obj->set_ME($this);
			// загрузочные настройки
			if(isset($this->_SETTINGS['mod_params'][$mod]))
			{
				$mod_obj->set_load_settings($this->_SETTINGS['mod_params'][$mod]);
			}
			else 
			{
				$mod_obj->set_load_settings();
			}
			
			$req_modules = $mod_obj->required();
			foreach($req_modules as $req)
			{						
				if(!$this->load_module($req))
				{
					$this->gen_error("Module $req is disabled or not exists");
					return false;					
				}
			}
					
			$this->_MODULES_OBJS[$mod] = $mod_obj;
		}
		catch(Exception $exc)
		{
			return false;
		}

		return true;
	}
	// сераиализовать модули, которые нужно
	public function save_modules()
	{
		foreach ($this->_MUST_SAVE as $_mod)
		{
			$this->serialize_module($_mod);
		}
	}
	// сериализовать модуль 
	function serialize_module($modname)
	{
		$_mod_ser = serialize($this->_MODULES_OBJS[$modname]);
		if(!isset($_SESSION['_SER_MODS']))
		{
			$_SESSION['_SER_MODS']=[];
		}
		$_SESSION['_SER_MODS'][$modname]=$_mod_ser;
	}
	
	function unserialize($modname)
	{
		if(isset($_SESSION['_SER_MODS'][$modname]))
	   		return unserialize($_SESSION['_SER_MODS'][$modname]);
		return null;
	}
	// обращение прямое к функции модуля
	public function call_module($modname,$method,&$params)
	{
		if(isset($this->_MODULES_OBJS[$modname]))
		{
			return $this->_MODULES_OBJS[$modname]->$method($params);
		}
		else 
		{
			$this->err_log("Module not exists");
			return null;
		}
	}
	
	public function _call_module($modname,$method,$params)
	{
		if(isset($this->_MODULES_OBJS[$modname]))
		{
			return $this->_MODULES_OBJS[$modname]->$method($params);
		}
		else
		{
			$this->err_log("Module not exists");
			return null;
		}
	}
	
	function gen_error($err)
	{
		$this->call_event('mlam_error');
		echo "<font color='red'><h1>$err</h1></font>";
		die();
	}
	
	function err_log($err)
	{
		
	}

	function module_loaded($_mod)
	{
		return (isset($this->_MODULES_OBJS[$_mod]));
	}
	
	function module_exists($modname)
	{
		return (file_exists("./modules/$modname/index.php"));		
	}
	// check if module enabled
	function module_enabled($modname)
	{
		$res = false;
		foreach ($this->_SETTINGS['enable_modules'] as $idx => $word)
		{
			$res = match_mask($word,$modname);
			if($res) return  $res;
		}
		return $res;
	}	
	// get list of enabled modules
	function get_enabled_modules()
	{
		$modlist=[];
		$modules = get_files_in_folder('modules',['dirs'=>true,'basename'=>true]);
		foreach ($modules as $mod)
		{		
			if($this->module_enabled($mod))
			{
				$modlist[]=$mod;
			}
		}
		return $modlist;
	}
	
	function get_modules_by_mask($mask)
	{
		$modules = $this->get_enabled_modules();
		$modlist=[];
		foreach ($modules as $mod)
		{
			if(match_mask($mask, $mod))
			{
				$modlist[]=$mod;
			}
		}
		return $modlist;
	}
	// call event
	function call_event($_event,$_params=[],$priority=null)
	{
		$mod_keys = array_keys($this->_MODULES_OBJS);
		$mod_keys_new = [];
		if($priority!=null) // если задан приоритет отработки модулей
		{			
			foreach ($priority as $pr_element)
			{
				if(is_mask($pr_element))
				{
					$bymask = $this->get_modules_by_mask($mask);
					$mod_keys_new = array_merge($mod_keys_new,$bymask);
				}
				else 
				{
					$mod_keys_new[]=$pr_element;
				}				
			}
			
			foreach ($mod_keys as $idx => $mod)
			{
				if(!in_array($mod, $mod_keys_new))
				{
					$mod_keys_new[]=$mod;
				}
			}
			
			$mod_keys = $mod_keys_new;
		}
		// отрабатываем
		$ev_results = [];
		foreach ($mod_keys as $idx => $modname)
		{
			$mod_obj = $this->_MODULES_OBJS[$modname];
			$ev_func_name = $this->event_function_name($_event);
			if(method_exists($mod_obj, $ev_func_name))
			{
				$ev_res = $mod_obj->$ev_func_name($_params);
				$ev_results[$modname]=$ev_res;
			}
		}
		return $ev_results;
	}

	function call_event_sess($_event,$_params=[],$priority=null)
	{
		
		if(!isset($_SESSION['events']))
		{
			$_SESSION['events']=[];
		}
		$_SESSION['events'][]=['event'=>$_event,'params'=>$_params,'priority'=>$priority];
	}
	
	function exe_sess_events()
	{
	//	unset($_SESSION['events']);
		if(isset($_SESSION['events']))
		{
			foreach ($_SESSION['events'] as $_ev)
			{
				$this->call_event($_ev['event'],$_ev['params'],$_ev['priority']);
			}
			unset($_SESSION['events']);
		}
	}
	
	function event_function_name($ev_name)
	{
		return strtr($ev_name,".","_");
	}

}

