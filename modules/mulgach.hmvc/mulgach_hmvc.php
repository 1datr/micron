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
			
			public function mulgach_onpage()
			{
				$req_str = $_REQUEST['r'];
				$hmvc_req = new HMVCRequest($req_str);
				print_r($hmvc_req);
				$_req2 = $hmvc_req->get_alternative();
				print_r($_req2);
			//	echo "<h2>MMMN</h2>";
			}
	
		}	
}