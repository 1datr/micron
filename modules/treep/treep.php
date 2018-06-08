<?php
namespace modules\treep{
use Core;

class Module extends Core\Module 
	{		
		
		VAR $ERROR_NO = 0;
		var $ERROR_TEXTS = [1=>'Bad tree'];
		
		public function get_error()
		{
			return $this->ERROR_NO;
		}
		
		public function get_err_text()
		{
			if(isset($this->ERROR_TEXTS[$this->ERROR_NO]))
			{				
				return $this->ERROR_TEXTS[$this->ERROR_NO];
			}
			return "";
		}
		
		public function compile($params)
		{
			$n_starts=[];
			preg_match_all($params['nstart'], $params['code'],$n_starts, PREG_OFFSET_CAPTURE);
			
			$n_ends=[];
			preg_match_all($params['nend'], $params['code'],$n_ends, PREG_OFFSET_CAPTURE);
			
			//print_r($n_starts);
			
			$root = new tnode(true);
			//print_r($n_ends[0]);
			if(count($n_ends[0])>0)
			{
				if(count($n_ends[0])!=count($n_starts[0]))
				{
					$this->ERROR_NO = 1;
					return null;
				}
				
				if($n_ends[0][0][1]<$n_starts[0][0][1])
				{
					$this->ERROR_NO = 1;
					return null;
				}
				
				$curr_node = $root;
				$submap = [];
				$idx_start = 0;
				$idx_end =0;
				$curr_pos=0;
				
				// основной цикл формирования дерева
				while($idx_start<count($n_starts))// ($n_starts[0] as $_node_start)
				{
					if($idx_start==0)
					{
						//строка перед тегом
						$substr = substr($params['code'],$pos,$n_starts[0][$idx_start][1]-$pos);
						$pos = $n_starts[0][$idx_start][1]+strlen($n_starts[0][$idx_start][0]);
						$curr_node->add_item($substr);
						
						$newnode = new tnode();
						$newnode->_PARENT = $curr_node;
						$newnode->_POS_STRAT = $n_starts[0][$idx_start][1];
						$newnode->_POS_START_END = $pos;
						
						// расставляем регулярные выражения начала
						foreach ($n_starts as $start)
						{
							$newnode->_START_TAG_REGEXP_RESULT[]=$start[$idx_start][0];
						}
						
						$curr_node->add_item($newnode);						
						
						$curr_node = $newnode;
						
						$idx_start++;
					}
					else 
					{
						if($n_ends[0][$idx_end][1]< $n_starts[0][$idx_start][1] ) // пришел конец тега 
						{
							//строка перед тегом
							$substr = substr($params['code'],$pos,$n_ends[0][$idx_end][1]-$pos);
							
							$pos = $n_ends[0][$idx_end][1]+strlen($n_ends[0][$idx_end][0]);
							$curr_node->add_item($substr);
							
							$curr_node->_POS_END = $n_ends[0][$idx_end][1];
							$curr_node->_POS_END_END = $pos;
							
							foreach ($n_ends as $end)
							{
								$curr_node->_END_TAG_REGEXP_RESULT[]=$end[$idx_end][0];
								//$curr_node->_END_TAG_REGEXP_RESULT[]=$n_ends[0][$idx_end][0];
							}							
							
							$curr_node = $curr_node->_PARENT;
							$idx_end++;
						}
						else		// пришел новый тег 
						{
							//строка перед тегом
							$substr = substr($params['code'],$pos,$n_starts[0][$idx_start][1]-$pos);
							$curr_node->add_item($substr);
							$pos = $n_starts[0][$idx_start][1]+strlen($n_starts[0][$idx_start][0]);
							
							$newnode = new tnode();
							$newnode->_PARENT = $curr_node;
							$newnode->_POS_STRAT = $n_starts[0][$idx_start][1];
							$newnode->_POS_START_END = $pos;
							
							foreach ($n_starts as $start)
							{
								$newnode->_START_TAG_REGEXP_RESULT[]=$start[$idx_start][0];
							}
							
							$newnode->_POS_START_END = $newnode->_POS_STRAT+strlen($n_starts[0][$idx_start][0]);
							$curr_node->add_item($newnode);
							
							$curr_node = $newnode;
							
							$idx_start++;
						}
					}
					
				}
					
				
				$substr = substr($params['code'],$pos,strlen($params['code'])-$pos);
					
				$pos = strlen($params['code']);
				$root->add_item($substr);
				
				$root->_POS_END = strlen($params['code']);
				$root->_POS_END_END = strlen($params['code']);
					
				return $root;
			}
			else 
			{
				$root->add_item($params['code']);
				return $root;
			}
			/*$order_buff=[]
			for($i=0;$i<count($n_starts[0]);$i++)
			{
				
			}
			*/		
			
			
		}
	
	}	
	
	class tnode
	{
		VAR $_ITEMS=[];
		VAR $_START_TAG_REGEXP_RESULT=[];
		VAR $_END_TAG_REGEXP_RESULT=[];
		VAR $ROOT=false;
		VAR $_POS_STRAT=0;
		VAR $_POS_START_END=0;
		VAR $_POS_END=0;
		VAR $_POS_END_END=0;
		VAR $_PARENT=null;
		
		function add_item($item)
		{
			$this->_ITEMS[]=$item;
		}
		
		function __construct($root=false)
		{
			$this->ROOT = $root;
		}
		
		function add_strat_regexp($arr)
		{
			$this->_START_TAG_REGEXP_RESULT[]=$arr;
		}
		
		function add_end_regexp($arr)
		{
			$this->_START_TAG_REGEXP_RESULT[]=$arr;
		}
		
		function close()
		{
			
		}
		
		
	}
}