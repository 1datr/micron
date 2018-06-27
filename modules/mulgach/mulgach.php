<?php
namespace modules\mulgach{
use Core;

class Module extends Core\Module 
	{		
		VAR $_MUL_PATH='';
		VAR $_CURR_CONF_DIR='';
		VAR $_CFG_PATH='';
		VAR $_CONFIG="";
		VAR $_EP='';
		
		function base_html_page_beforepage(&$params)
		{
		/*	if($_REQUEST['r']=='r777')
				echo "@@###@@";	
			$params['_page_path'] = "./pages/about.php";*/
		}
		
		public function required()
		{
			return ['base.html'];
		}
		
		public function info()
		{
			return ['_CFG_PATH'=>$this->_CFG_PATH,
					'_CURR_CONF_DIR'=>$this->_CURR_CONF_DIR,
					'_CONFIG'=>$this->_CONFIG,
					'_EP'=>$this->_EP,
			];
		}
		
		private function load_mulgach()
		{
			$this->_MUL_PATH = $this->_L_SETTINGS['muldir'];
			$this->_CFG_PATH = url_seg_add($this->_MUL_PATH,'conf.php');	
			$this->_EP = (isset($GLOBALS['EP'])?$GLOBALS['EP']:'frontend');
			
			$fp_conf = new Core\FilePair($this->_CFG_PATH);
			if(isset($GLOBALS['_MUL_CFG']))			
				$this->_CONFIG = $GLOBALS['_MUL_CFG'];
			else 
				$this->_CONFIG = $fp_conf->get_settings()['_MUL_CONF'];
			$this->_CURR_CONF_DIR = url_seg_add($this->_PATH,"conf");
			//echo $this->_CURR_CONF_DIR." > ";
			//print_r($this);
		} 
		// при загрузке 
		function base_html_onbody($params)
		{
			$this->load_mulgach();
			$this->call_event('onpage',[]);			
		}
		
		function core_onload()
		{
			//$this->MLAM->_call_module('base.modutil','create_module',['modname'=>'mulgach.db.mysqli']);
		/*	$this->MLAM->_call_module('base.modutil','create_module',['modname'=>'mulgach.db']);
			$this->MLAM->_call_module('base.modutil','create_module',['modname'=>'mulgach.mysqli']);*/
			
		}
		
		public function AfterLoad()
		{
			$this->load_lib('BaseController');
			$this->load_lib('scaff_api/index');
		
		}

	}	
}