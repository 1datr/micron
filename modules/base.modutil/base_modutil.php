<?php
namespace modules\base\modutil{
use Core;

class Module extends Core\Module 
	{		
		VAR $ctr;	
		
		public static function settings()
		{
			return ['sess_save'=>true];
		}
		
		function onload_basic()
		{
			$this->ctr=1;
		}
		
		function base_html_onbody($params)
		{
			$fruits=['apple','pear','cherry','arbuz'];
			
			__foreach($fruits, function($idx,$val)
			{
				
			} );
			
			echo "<br /><font color=\"blue\" >{$this->ctr}</font>";
			if($this->ctr==10)
			{
				$this->call_event_sess('on_counted', ['ctr'=>$this->ctr]);
			}
			$this->ctr++;
			$this->ctr%=20;
		}
		
		public function create_module($params)
		{
			$mod_dir = "./modules/".$params['modname'];
			if(!is_dir($mod_dir))
			{
				mkdir($mod_dir);
			}
			$mod_file_name = $mod_dir."/".strtr($params['modname'],['.'=>'_']).".php";			
			$_params=['modname_namespace'=>strtr($params['modname'],['.'=>'\\'])];
			file_put_contents($mod_file_name, parse_code_template(__DIR__."/phpt/module.phpt",$_params));
		}
		
		function base_modutil_on_counted($params)
		{
			echo "<h3>Hello, world</h3>";
		}
	
	}	
}