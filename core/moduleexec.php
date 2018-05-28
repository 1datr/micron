<?php
// modules executer class

class ModuleExecuter {

	var $_MODULES_OBJS=[];

	function load_modules()
	{
		$modules = get_files_in_folder('modules',['dirs'=>true,'basename'=>true]);
		foreach ($modules as $mod)
		{
				
			require_once "./modules/$mod/index.php";
				
			$mod_obj = $this->make_module_obj($mod);
		}
	}

	function get_mod_class_name($mod)
	{
		return "modules\\".str_replace('.', '\\', $mod)."\Module";
	}

	// создать объект модуля
	function make_module_obj($mod)
	{
		try{
			if($this->module_loaded($mod))
				return true;
					
				$mod_class = $this->get_mod_class_name($mod);
					
				//echo $mod_class;
					
				$mod_obj = new $mod_class();
				$mod_obj->_MOD_NAME = $mod;
				$mod_obj->set_ME($this);
				$req_modules = $mod_obj->required();
				foreach($req_modules as $req)
				{
						
					if(!$this->make_module_obj($req))
						return false;
				}
					
				$this->_MODULES_OBJS[$mod] = $mod_obj;
		}
		catch(Exception $exc)
		{
			return false;
		}

		return true;
	}

	function module_loaded($_mod)
	{
		return (isset($this->_MODULES_OBJS[$_mod]));
	}

	function call_event($_event,$_params=[])
	{
		foreach ($this->_MODULES_OBJS as $modname => $mod_obj)
		{
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
