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
			$this->ctr++;
			$this->ctr%=20;
		}
	
	}	
}