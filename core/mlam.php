<?php
// module loader and manager

class MLAM {

	var $_MODULES_OBJS=[];
	var $_SETTINGS=[];

	function load_modules()
	{
		// load modules settings
		if(file_exists("./modules/settings.php"))
		{
			include "./modules/settings.php";
			$this->_SETTINGS = $enabled_modules;
		}
		//
		$modules = get_files_in_folder('modules',['dirs'=>true,'basename'=>true]);
		foreach ($modules as $mod)
		{							
				
			$mod_make_res = $this->load_module($mod);
		}
	}

	function get_mod_class_name($mod)
	{
		return "modules\\".str_replace('.', '\\', $mod)."\Module";
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
			
			if(file_exists("./modules/$mod/index.php"))
			{
				require_once "./modules/$mod/index.php";
			}
			else 
				return false;
			
			
					
			$mod_class = $this->get_mod_class_name($mod);
								
			$mod_obj = new $mod_class();
			$mod_obj->_MOD_NAME = $mod;
			$mod_obj->set_ME($this);
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
	
	function call_module($modname,$method,&$params)
	{
		if(isset($this->_SETTINGS[$modname]))
		{
			return $this->_SETTINGS[$modname]->$method($params);
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
	
	function module_enabled($modname)
	{
		$res = false;
		foreach ($this->_SETTINGS as $idx => $word)
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
		foreach ($mod_keys as $idx => $modname)
		{
			$mod_obj = $this->_MODULES_OBJS[$modname];
			$ev_func_name = $this->event_function_name($_event);
			if(method_exists($mod_obj, $ev_func_name))
			{
				$mod_obj->$ev_func_name($_params);
			}
		}
	}

	function event_function_name($ev_name)
	{
		return strtr($ev_name,".","_");
	}

}

