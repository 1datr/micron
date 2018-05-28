<?php
namespace modules\base\html\js{
use Core;

class Module extends Core\Module 
	{		
		
		function base_html_onbody($params)
		{
			echo "<h1>Hello, World</h1>";
		}
		
		function base_html_onhead($params)
		{
			$this->call_event('onjs', []);
		}
		
		function core_onload()
		{
			
		}

	}	
}