<?php
namespace modules\base\html\page{
use Core;

class Module extends Core\Module 
	{		
		VAR $_DEF_SETTINGS=['active'=>true,];
		
		function base_html_onbody($params)
		{
			if($this->_L_SETTINGS['active'])
			{
				if(empty($_REQUEST['r']))
					$_REQUEST['r']='index';
				
				$mod_html=$params['mod_html'];
				$PARAMS['_page_path'] = './pages/'.$_REQUEST['r'].".php";
				
				$this->call_event('beforepage', ['params'=>$PARAMS]);			
				include $PARAMS['_page_path'];
				$this->call_event('afterpage', ['params'=>$PARAMS]);
			}
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