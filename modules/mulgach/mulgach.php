<?php
namespace modules\mulgach{
use Core;

class Module extends Core\Module 
	{		
		
		function base_html_page_beforepage(&$params)
		{
		/*	if($_REQUEST['r']=='r777')
				echo "@@###@@";	
			$params['_page_path'] = "./pages/about.php";*/
		}
		
		function base_html_onbody($params)
		{
			$this->call_event('onpage',[]);
			//echo "<h4>__ MULGAH __</h4>";
		}
		
		function core_onload()
		{
			//$this->MLAM->_call_module('base.modutil','create_module',['modname'=>'mulgach.db.mysqli']);
		/*	$this->MLAM->_call_module('base.modutil','create_module',['modname'=>'mulgach.db']);
			$this->MLAM->_call_module('base.modutil','create_module',['modname'=>'mulgach.mysqli']);*/
			
		}
		
		public function AfterLoad()
		{
			
		
		}

	}	
}