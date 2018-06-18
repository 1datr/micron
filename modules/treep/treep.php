<?php
/*
    ������������� ����������� ������
 
  	����������� ����� ����
 /# {#rz
		#/	
		{@
		{#reef  #}
		@}
		
xxx		
		{#if c=\"xx\" (x==0)
		{#then 
				x=x+1;
			#}
		{#else 
				x=x+2;
			#}
// #}		
		#}
xxx
		{#foreach(arr_x as idx => x)

		#}
ddd 	
  	
 � ����������� ��������� �� tnode ������ �� ���������� � ��� ��� �������� ��������� � �������� ���, ����������� � ������������ ������������������, ��� ����� 
 �������������� ��� ������ �����, �������� �� ��������� ������� � ��� ��������� ���� �������� �����. 
  
 * */
namespace modules\treep{
use Core;
use \modules\base\tree\HNnumerator;

class Module extends Core\Module 
	{		
		
		VAR $ERROR_NO = 0;
		var $ERROR_TEXTS = [1=>'Parse error'];
		VAR $_COMMENTS_MAP=[];
		
		// ����� ������ ����� ��������� �������� ��������
		public function get_error()
		{
			return $this->ERROR_NO;
		}
		
		// ����� ��������� ������
		public function get_err_text()
		{
			if(isset($this->ERROR_TEXTS[$this->ERROR_NO]))
			{				
				return $this->ERROR_TEXTS[$this->ERROR_NO];
			}
			return "";
		}
		
		public function required()
		{
			return ['base.tree'];
		}
		
		
		private function calc_open_close($buf,&$count_open,&$count_closed)
		{
			$count_open=0;
			$count_closed=0;
			foreach($buf as $str => $point)
			{
				if($point['type']=='open')
				{
					$count_open++;
				}
				elseif($point['type']=='closed')
				{
					$count_closed++;
				}
			}
		}
		// ������� ����������� �� ������ ���� �����
		private function clear_comments($params,&$the_str)
		{
			if($params['delete_comments'])
			{
				if(is_array($params['comments']))
				{
					foreach ($params['comments'] as $idx => $_str)
					{
						$the_str = preg_replace($_str, "", $the_str);
					}
				}
				elseif(is_string($params['comments'])) 
				{
					$the_str = preg_replace($params['comments'], "", $the_str);
				}
			}
		}
		
		private function make_comments_map($params)
		{
			$_COMMENTS_MAP=[];
			if(is_array($params['comments']))
			{
				foreach ($params['comments'] as $idx => $_str)
				{
					$_matches=[];
					preg_match_all($_str, $params['code'],$_matches, PREG_OFFSET_CAPTURE);
					
					//$params['code'] = preg_replace($_str, "", $params['code']);
				}
			}
			elseif(is_string($params['comments']))
			{
				$_matches=[];
				preg_match_all($_str, $params['code'],$_matches, PREG_OFFSET_CAPTURE);
				//$params['code'] = preg_replace($params['comments'], "", $params['code']);
			}
			
			return $_COMMENTS_MAP;
		}
		
		public function AfterLoad()
		{
			$this->load_lib('nodes');		
		}
		
		private function get_shields_areas($params)
		{
			$shields = [];
			if(isset($params['shields']))
			{				
				if(is_array($params['shields']))  
				{
					$ptrn='/';
					foreach($params['shields'] as $shidx => $shld)
					{						
						$ptrn = '/'.$shld[0].'(.*)'.$shld[1].'/Us';
					
						$_shields=[];
						preg_match_all($ptrn, $params['code'],$_shields, PREG_OFFSET_CAPTURE);
						foreach ($_shields[0] as $_shld)
						{
							$_code = $_shld[0];
							
							// ��������� �� �������� �� ������� ��� � ���� �����-������ �� ��� ������������
							/*
							foreach ($shields as $shld_item)
							{
								
							}*/
							
							$shields[]=['start'=>$_shld[1],
									'end'=>$_shld[1]+strlen($_shld[0]),
									'code'=>$_code
							];
						}
					}																		
				}
			}			
			
			// ��������� �������	
			$idx_to_unset=[];
			foreach ($shields as $idx1 => $shld1)
			{
				foreach ($shields as $idx2 => $shld2)
				{
					if( ($idx1!=$idx2)&&(!in_array($idx1, $idx_to_unset)) )
					{
						if(($shld1['start']<=$shld2['start'])&&($shld2['end']<=$shld1['end']))
						{
							$idx_to_unset[]=$idx1; 
							break;
						}
					}
				}	
			}
			foreach ($idx_to_unset as $_idx)
			{
				unset($shields[$_idx]);
			}
			return $shields;
		}
		
		private function delete_shilds($params,&$str)
		{
			if(isset($params['shields']))
			{
				if(is_array($params['shields']))							
				{					
					foreach($params['shields'] as $shidx => $shld)
					{
						def_options(['clear'=>true], $shld);
						if($shld['clear'])
						{
							$_shields=[];
							$ptrn = '/'.$shld[0].'(.*)'.$shld[1].'/sm';
							$str = preg_replace($ptrn, '$1', $str);
						}
					}
				}
			}
		}
		
		private function filter_by_map($map,&$pointbuf)
		{
			// ������� �����, ����������� � �������������� ��������
			$to_delete=[];
			foreach($pointbuf as $str => $info)
			{
				foreach($map as $map_item)
				{
					if(($map_item['start']<=$str)&&($str<=$map_item['end']))	// �������� � ������������ ������
					{
						$to_delete[]=$str;	// ������� � ��������� � ���������
						break;
					}
				}
			}
			foreach ($to_delete as $_str)
			{
				unset($pointbuf[$_str]);
			}
		}
		/* ��������������� � ������
		 $params - ������������� ������ ���������� �� ���������� ������� 		 
		 	��������� :
		 code - ��������������� ������ ����
		 nstart - ���������� ��������� ��������� �������
		 nend  - ���������� ��������� �������� ������� 
		 comments - ���������� ��������� ������ ������������ (������������ � �������������) ���������� ������ �����
		 shields - ������ ������������ ������������������� [���. ���. ������, ���. ���. �����]
		 	���������-������� :
		 onmapready($pointbuf) - ����� ���������� �����  
		 onnoderady($curr_node) - ����� ���������� ����
		 	  
		 * */
		public function compile($params)
		{
			def_options(['comments'=>['#\/\*.*\*\/#s','#\/\/.*$#m'],
					'delete_comments'=>true,
			],$params);
			
			$comments_map = $this->make_comments_map($params);
			//$this->clear_comments($params);
			
			$n_starts=[];
			preg_match_all($params['nstart'], $params['code'],$n_starts, PREG_OFFSET_CAPTURE);
			
			$n_ends=[];
			preg_match_all($params['nend'], $params['code'],$n_ends, PREG_OFFSET_CAPTURE);
			
			//print_r($n_starts);
			
			$root = new tn_object(true);
			$numerator = new \modules\base\tree\HNnumerator();
			$root->number = $numerator->getText();
			$root->numerator_obj = $numerator;
			//print_r($n_ends[0]);
			if(count($n_ends[0])>0)
			{
							
				if($n_ends[0][0][1]<$n_starts[0][0][1])
				{
					$this->ERROR_NO = 1;
					return null;
				}
				
				$curr_node = $root;
				$submap = [];
				$idx_start = 0;
				$idx_end =0;
				$pos=0;
				
				$pointbuf=[];
				for($idx=0;$idx<count($n_starts[0]);$idx++)
				{
					$point=['buf'=>[],'type'=>'open'];
					foreach ($n_starts as $nst)
					{
						$point['buf'][]=$nst[$idx][0];
					}
					
					$pointbuf[$n_starts[0][$idx][1]]=$point;
				}
				
				for($idx=0;$idx<count($n_ends[0]);$idx++)
				{
					$point=['buf'=>[],'type'=>'closed'];
					foreach ($n_ends as $nend)
					{
						$point['buf'][]=$nend[$idx][0];
					}
						
					$pointbuf[$n_ends[0][$idx][1]]=$point;
				}
				
				// �������������� �������
				$shilds = $this->get_shields_areas($params);
				
				ksort($pointbuf);
				// ������� �����, ����������� � �������������� ��������
				$this->filter_by_map($shilds,$pointbuf);
				$this->filter_by_map($comments_map,$pointbuf);
																			
				if(isset($params['onmapready']))
				{
					$params['onmapready']($pointbuf);
				}
						
				$count_open = 0;
				$count_closed = 0;
				$this->calc_open_close($pointbuf,$count_open,$count_closed);
				if($count_closed!=$count_open)
				{
					$this->ERROR_NO=1;
					return null;
				}
				
				// �������� ���� ������������ ������ 
				$curr_node = $root;
				$last_pos = 0;
				
				foreach ($pointbuf as $pos => $point)
				{
					if($point['type']=='open')
					{
						$substr = substr($params['code'],$last_pos,$pos-$last_pos);
						$this->delete_shilds($params,$substr);
						$this->clear_comments($params,$substr);
						
						$substr_node = new tn_text($substr);
						
						
						
						$curr_node->add_item($substr_node);
						
						$newtag = new tn_object();
						$newtag->_POS_STRAT = $pos;
						$newtag->_POS_START_END = $pos+strlen($point['buf'][0]);
						$last_pos = $newtag->_POS_START_END; 
						$newtag->_START_TAG_REGEXP_RESULT = $point['buf'];
						$newtag->_PARENT = $curr_node;
						
						
						
						$curr_node->add_item($newtag);
						
						$curr_node = $newtag;
					}
					elseif($point['type']=='closed')
					{
						$substr = substr($params['code'],$last_pos,$pos-$last_pos);
						$this->delete_shilds($params,$substr);
						$this->clear_comments($params,$substr);
						
						$substr_node = new tn_text($substr);

						
						$curr_node->add_item($substr_node);
						
						$curr_node->_POS_END = $pos;
						$curr_node->_POS_END_END = $pos+strlen($point['buf'][0]);
						
						$last_pos = $curr_node->_POS_END_END;
						
						$curr_node->_END_TAG_REGEXP_RESULT = $point['buf'];
						
						// ��������� ������� ��� ��������� �������� ����
						if(isset($params['onnoderady']))
						{
							$params['onnoderady']($curr_node);
						}
						
						$curr_node = $curr_node->_PARENT;						
						
					}
				}
				
				$substr = substr($params['code'],$last_pos,strlen($params['code'])-$last_pos);
				$this->delete_shilds($params,$substr);
				$this->clear_comments($params,$substr);
				
				$substr_node = new tn_text($substr);
					
				$pos = strlen($params['code']);
				$root->add_item($substr_node); // �������� �����
				
				$root->_POS_END = strlen($params['code']);
				$root->_POS_END_END = strlen($params['code']);
					
				return $root;
			}
			else 
			{
				$root->add_item($params['code']);
				return $root;
			}
						
		}
	
	}	
	
}