<?php
namespace modules\base\php\managed{
use Core;

class Module extends Core\Module 
	{		
		public function compile($_params)
		{
			$code_points = $this->get_code_points($_params['code']);
			$tree = $this->MLAM->_call_module('treep','compile',[
					'code'=>$_params['code'],
					'nstart'=>'/((while|for|foreach|if|elseif|switch|try|catch|finally)\((.+)\).*$\s*\{)|((while|for|foreach|if|elseif|switch)\((.+)\).*\s*\{)|(else\s*\{)/',
					'nend'=>'/\}/',
					'comments'=>['#\/\*.*\*\/#Us','#\/\/.*$#m'],
					'shields'=>[
							['\?>','<\?php','clear'=>false],
							['\?>','<\?=','clear'=>false],
							['\?>','$','clear'=>false],
							['\/\*.*CRITICAL\s+STRAT.*\*\/','\/\*.*CRITICAL\s+END.*\*\/','clear'=>false]
					],
				/*	'onmapready'=>function(&$pbuf) use($code_points)
					{
						foreach($pbuf as $idx => $buf)
						{
							$in_code=false;
							foreach ($code_points as $p)
							{
								if(($p['start']<=$idx)&&($idx<=$p['end']))
								{
									$in_code=true;
									break;
								}
							}
							if(!$in_code)
							{
								unset($pbuf[$idx]);
							}
						}
					}*/
					
			]);
			if($tree==null)
			{
				echo "<h3>".$this->MLAM->_call_module('treep','get_err_text',[])."</h3>";
			}
			else
			{
				echo "<h3>PARSING SUCCESSFULL</h3>";
				// Строим дерево кода
				$root = new root_node();
				$curr_node = $root;
				$tree->walk(function($node) use($curr_node)	
					{
						/*echo $node->number." <br /> ";
						print_r($node->_START_TAG_REGEXP_RESULT);*/
					}
				);
				//print_r($tree);
			}
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
	
	class code_node 
	{
		VAR $number;
		VAR $numerator_obj;
		function __construct()
		{
				
		}
	}
	
	class root_node extends code_node
	{
	
		function __construct()
		{
	
		}
	}
	
	class simple_code extends code_node
	{
		VAR $code;
		
		function __construct()
		{
				
		}
	}
	
	class operator extends code_node
	{
		VAR $op;
		VAR $argument;
		
		function __construct()
		{
			
		}
	}
}

