<?php
namespace Core {

	class FilePair
	{
		VAR $name;
		VAR $ser_file_path;
		VAR $php_file_path;
		VAR $_SETTINGS;
		
		public function __construct($kpname)
		{
			$finfo = pathinfo($kpname, PATHINFO_DIRNAME | PATHINFO_BASENAME | PATHINFO_EXTENSION | PATHINFO_FILENAME);
			//print_r($finfo);
			$this->php_file_path = $finfo['dirname']."/".$finfo['filename'].".php";
			$this->ser_file_path = $this->php_file_path.".ser";
			if(!file_exists($this->php_file_path) && !file_exists($this->ser_file_path))
			{
				return;
			}		
			
			if(file_exists($this->php_file_path))
			{
				$this->compile_ser_if_changed();
			}
			
			if(file_exists($this->ser_file_path))
			{
				$this->_SETTINGS = unserialize(file_get_contents($this->ser_file_path));
			}
		}
		
		function compile_ser_if_changed()
		{
			$time_ser = filemtime($this->ser_file_path);
			
			$time_php = filemtime($this->php_file_path);
			if($time_php>$time_ser)
			{
				$this->compile_ser();
			}
		}
		
		function compile_ser()
		{
			include $this->php_file_path;
			$this->_SETTINGS = $_SETTINGS;
			$this->save();
		}
		
		function save()
		{
			$serialized = serialize($this->_SETTINGS);
			file_put_contents($this->ser_file_path, $serialized);
		}
		
		function get_settings()
		{
			return $this->_SETTINGS;
		}
	}

}