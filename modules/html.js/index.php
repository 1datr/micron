<?php
namespace modules\html\js{
use Core;

class Module extends Core\Module 
	{		
		
		
		
		function html_onbody($params)
		{
			echo "<h1>Hello, World</h1>";
		}
		
		function core_onload()
		{
			
		}

	}	
}