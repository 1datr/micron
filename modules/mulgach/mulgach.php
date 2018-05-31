<?php
namespace modules\mulgach{
use Core;

class Module extends Core\Module 
	{		
		
		function base_html_page_beforepage(&$params)
		{
			if($_REQUEST['r']=='r777')
				echo "@@###@@";	
			$params['_page_path'] = "./pages/about.php";
		}
		
		function base_html_onhead($params)
		{

		}
		
		function core_onload()
		{
			
		}

	}	
}