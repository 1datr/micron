<?php
namespace modules\mulgach\hmvc{
use Core;

class Module extends Core\Module 
	{		
		
		public function AfterLoad()
		{
			$this->load_lib('hmvc_request');
		
		}
		
		public function mulgach_onpage()
		{
			echo "<h2>MMMN</h2>";
		}

	}	
}