<?php
namespace modules\base\tree{
use Core;

	class Module extends Core\Module 
	{		
		public function AfterLoad()
		{
			$this->load_lib('numerator');
			$this->load_lib('tree');
		}

	}	
	
	
	
}