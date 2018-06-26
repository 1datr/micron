<?php
namespace modules\mulgach\hmvc\scaffapi{
	use Core;

	class scaff_module
	{
		VAR $_PATH;
		VAR $_ERROR=false;
		VAR $_NAME;
		
		function __construct($modname,$opts=array())
		{
			$this->_NAME=$modname;
			def_options(array('mod_dir'=>'modules','rewrite'=>false), $opts);
			
			GLOBAL $_BASEDIR;
			$this->_PATH = url_seg_add($_BASEDIR,$opts['mod_dir'],$this->_NAME);
			
			if(file_exists($this->_PATH) && !$opts['rewrite'] )
				return;
		}
		
		function getplugin($plgname)
		{
			
		}
		
		function plugin_exists($plgname)
		{
			$plg_path = url_seg_add($this->_PATH,'plugins',$plgname,'index.php');
		//	mul_dbg($plg_path);
			return file_exists($plg_path);
		}
		
		function create_plugin($plgname,$opts=array())
		{
			def_options(array('rewrite'=>false), $opts);
			if( (!$this->plugin_exists($plgname)) || $opts['rewrite'])
			{
				//mul_dbg($opts);
				$newplg = new scaff_plugin($this, $plgname,$opts);
				return $newplg;
			}
			return null;
		}
		
	}

}