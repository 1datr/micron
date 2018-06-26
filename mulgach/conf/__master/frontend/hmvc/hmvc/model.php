<?php
namespace __master\Frontend;

class ModelHmvc extends \BaseModel
{
	
	function OnScenarioChange()
	{
		switch($this->scenario())
		{
		case 'total':{
			$this->_SETTINGS=array(
					'domen'=>'settings_total',
					'fields'=>[
							'conf'=>['Type'=>'text'],							
							'rewrite_all'=>['Type'=>'boolean'],
							'ignore_existing'=>['Type'=>'boolean'],
							'autofind_auth'=>['Type'=>'boolean','defval'=>true],
							'ep[frontend]'=>['Type'=>'boolean'],
							'ep[backend]'=>['Type'=>'boolean'],
							'ep[install]'=>['Type'=>'boolean'],
							'ep[rest]'=>['Type'=>'boolean'],
					],
					'required'=>['conf',]
					
			);	
			}; break;
		}
	}
	
}

