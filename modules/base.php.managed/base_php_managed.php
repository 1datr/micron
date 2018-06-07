<?php
namespace modules\base\php\managed{
use Core;

class Module extends Core\Module 
	{		
		public function compile($_params)
		{
			$struct = [];
			
			$_params['code']=preg_replace("/\/\*.+\*\//s", "", $_params['code']);
			
			$_params['code']=preg_replace("/\/\/.*$/s", "", $_params['code']);
			
			
			$matches_cycles=[];
			preg_match_all("/(foreach|for|while)\((.+)\)/Uis",$_params['code'],$matches_cycles, PREG_OFFSET_CAPTURE );
			//print_r($matches_cycles);
			// лишь те форичи где php-код, а не html
			$_code_regions = $this->get_code_points($_params['code']);
			foreach($matches_cycles[0] as $i => $_code_part)
			{
				if(!$this->in_code($_code_part[1],$_code_regions))
				{
					unset($matches_cycles[0][$i]);
					unset($matches_cycles[1][$i]);
					unset($matches_cycles[2][$i]);
				}
			}
			
			foreach($matches_cycles[0] as $i => $_code_part)
			{
				
			}
			
			print_r($matches_cycles);
		}
		
		function in_code($str_no, $code_regions)
		{
			foreach ($code_regions as $reg)
			{
				if(($reg['start']<=$str_no)&&($str_no<=$reg['end']))
				{
					return true;
				}
			}
			return false;
		}

		// фрагменты где код (не чистый вывод)
		function get_code_points($_code)
		{
			$matches_codes=[];
			$_code_regions = [];
			preg_match_all("/<\?php(.+)\?>|<\?=(.+)\?>/Uis",$_code,$matches_codes, PREG_OFFSET_CAPTURE );
			for($i=0;$i<count($matches_codes[0]);$i++)
			{
				$_code_regions[]=['start'=>$matches_codes[0][$i][1],'end'=>$matches_codes[0][$i][1]+strlen($matches_codes[0][$i][0])];
			}
			
			return $_code_regions;
		}
	}
	
	class mp_simply_code
	{
		VAR $code;
		
		function __construct($_code)
		{
			$this->code = $_code;
		} 
	
	}
	
	class mp_uncode	// не 
	{
		VAR $code;
		
		function __construct($_code)
		{
			$this->code = $_code;
		}
	}
	
	class mp_cycle
	{
		VAR $cycle_type = "";
		function __construct()
		{
			
		}
	}
}

