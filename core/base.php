<?php
namespace Core {

	class Module {
			
		var $MLAM=NULL;
		
		VAR $_MOD_NAME;
		
		VAR $MODE;
		
		function __construct($MODE='use')
		{
			$this->MODE=$MODE;
		}
		
		public function onload_basic()
		{
			
		}
		
		public static function settings()
		{
			return ['sess_save'=>false];
		}
		
		public function required()
		{
			return [];
			
		}
		
		public function set_ME($_ME)
		{
			
			$this->MLAM = $_ME;
		}
		
		public function call_event($_ev,$_params,$priorities=null)
		{
			$this->MLAM->call_event($this->_MOD_NAME.".".$_ev,$_params,$priorities);
		}
		
		public function call_event_sess($_ev,$_params,$priorities=null)
		{
			$this->MLAM->call_event_sess($this->_MOD_NAME.".".$_ev,$_params,$priorities);
		}
		
		public function install()
		{
			
		}
		
		public function uninstall()
		{
			
		}
		
	}

}