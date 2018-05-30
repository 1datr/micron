<?php
namespace modules\base\modutil{
use Core;

class Module extends Core\Module 
	{		
	
	public function required()
	{
		return ['base.phpt'];
	}
		
	public function make_mod()
	{
		
	}
	
	}	
}