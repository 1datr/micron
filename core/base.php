<?php
namespace Core {

	class Module {
			
		var $ME=NULL;
		
		VAR $_MOD_NAME;
		
		function __construct()
		{
		
		}
		
		public function required()
		{
			return [];
			
		}
		
		public function set_ME($_ME)
		{
			
			$this->ME = $_ME;
		}
		
		public function call_event($_ev,$_params)
		{
			$this->ME->call_event($this->_MOD_NAME.".".$_ev,$_params);
		}
		
	}

}