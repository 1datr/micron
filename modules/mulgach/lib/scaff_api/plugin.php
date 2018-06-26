<?php
namespace modules\mulgach\hmvc\scaffapi{
	use Core;
	
	class scaff_plugin
	{
		VAR $_PATH;
		VAR $_ERROR=false;
		VAR $_NAME;
		var $_module;
		var $index_file;
		
		function __construct(&$module_obj,$plgname,$opts=array())
		{
			$this->_NAME=$plgname;
			$this->_module = $module_obj;
			def_options(array('rewrite'=>false), $opts);
			
			$this->_PATH = url_seg_add($this->_module->_PATH,'plugins',$plgname);
			
		//	mul_dbg((file_exists($this->_PATH) && !$opts['rewrite'] ),false);
			
			if(file_exists($this->_PATH) && !$opts['rewrite'] )
				return;
			
			$this->create();
		}
		
		function create()
		{	
			x_mkdir(url_seg_add($this->_PATH,'/js'));
			x_mkdir(url_seg_add($this->_PATH,'/css'));
			$file_plugin=url_seg_add(__DIR__,'phpt/module/plugin.phpt');
			$vars = array('plgname'=>$this->_NAME);
			$this->index_file = url_seg_add($this->_PATH,'index.php');
			x_file_put_contents($this->index_file, parse_code_template($file_plugin,$vars));
		}
		
		
		
	}

}