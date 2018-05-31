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
			echo "<br /><font color=\"blue\" >{$this->ctr}</font>";
			if($this->ctr==10)
			{
				$this->call_event_sess('on_counted', ['ctr'=>$this->ctr]);
			}
			$this->ctr++;
			$this->ctr%=20;
		}
		
		function base_modutil_on_counted($params)
		{
			echo "<h3>Hello, world</h3>";
		}
	
	}	
}