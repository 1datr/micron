<?php
namespace modules\mulgach\hmvc {
	use Core;
	use modules\mulgach\HMVCRequest;
	
	class Module extends Core\Module 
		{		
			
			public function AfterLoad()
			{
				$this->load_lib('hmvc_request');
			
			}
			
			public function required()
			{
				return ['mulgach'];
			}
			
			public function request($req_str)
			{
				$req_str = $_REQUEST['r'];
				$hmvc_req = new HMVCRequest($req_str);
				//print_r($hmvc_req);
				$_req2 = $hmvc_req->get_alternative();
			}
			
			private function get_simply_request($hmv_req)
			{
				
			}
			
			public function mulgach_onpage()
			{
				$params = [];
				// параметры текущего запроса
				$mul_params = $this->MLAM->call_module('mulgach','info',$params);
				
				//print_r($mul_params);
				
				
				//print_r($_req2);
			//	echo "<h2>MMMN</h2>";
			}					
	
		}	
}