<?php
/*
    Универсальный древовидный парсер
 
  	Преобразует текст типа
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
  	
 в древовидную структуру из tnode исходя из соглашения о том как выглядит начальный и конечный тэг, комментарии и экранирующие последовательности, где текст 
 воспринимается как единый текст, несмотря на возможное наличие в нем начальных либо конечных тегов. 
  
 * */
namespace modules\treep{
use Core;

class Module extends Core\Module 
	{		
		
		VAR $ERROR_NO = 0;
		var $ERROR_TEXTS = [1=>'Parse error'];
		
		// номер ошибки после последней операции парсинга
		public function get_error()
		{
			return $this->ERROR_NO;
		}
		
		// текст последней ошибки
		public function get_err_text()
		{
			if(isset($this->ERROR_TEXTS[$this->ERROR_NO]))
			{				
				return $this->ERROR_TEXTS[$this->ERROR_NO];
			}
			return "";
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
		
		private function clear_comments(&$params)
		{
			if(is_array($params['comments']))
			{
				foreach ($params['comments'] as $idx => $_str)
				{
					$params['code'] = preg_replace($_str, "", $params['code']);
				}
			}
			elseif(is_string($params['comments'])) 
			{
				$params['code'] = preg_replace($params['comments'], "", $params['code']);
			}
		}
		
		private function get_shields_areas($params)
		{
			$shields = [];
			if(isset($params['shields']))
			{				
				if(is_array($params['shields']))  
				{
					foreach($params['shields'] as $shidx => $shld)
					{
						$_shields=[];
						$ptrn = '/'.$shld[0].'(.*)'.$shld[1].'/sm';
						preg_match_all($ptrn, $params['code'],$_shields, PREG_OFFSET_CAPTURE);
						foreach ($_shields as $shld)
						{
							$shields[]=['start'=>$shld[0][1],'end'=>$shld[0][1]+strlen($shld[0][0])];
						}
					}
				}
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
						$_shields=[];
						$ptrn = '/'.$shld[0].'(.*)'.$shld[1].'/sm';
						$str = preg_replace($ptrn, '$1', $str);					
					}
				}
			}
		}
		/* откомпилировать в дерево
		 $params - ассоциативный массив параметров со строковыми ключами 		 
		 	Параметры :
		 code - непосредственно строка кода
		 nstart - регулярное выражение стартовых токенов
		 nend  - регулярное выражение конечных токенов 
		 comments - регулярное выращение блоков комментариев (однострочных и многострочных) строкалибо массив строк
		 shields - массив экранирующих последовательностей [рег. выр. начало, рег. выр. конец]
		 	Параметры-события :
		 onmapready($pointbuf) - после построения карты  
		 onnoderady($curr_node) - после построения узла
		 	  
		 * */
		public function compile($params)
		{
			def_options(['comments'=>['#\/\*.*\*\/#s','#\/\/.*$#m']],$params);
			
			$this->clear_comments($params);
			
			$n_starts=[];
			preg_match_all($params['nstart'], $params['code'],$n_starts, PREG_OFFSET_CAPTURE);
			
			$n_ends=[];
			preg_match_all($params['nend'], $params['code'],$n_ends, PREG_OFFSET_CAPTURE);
			
			//print_r($n_starts);
			
			$root = new tn_object(true);
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
				
				// экранированные регионы
				$shilds = $this->get_shields_areas($params);
				
				// убираем точки, оказавшиеся в экранированных регионах
				foreach($pointbuf as $str => $info)
				{
					foreach($shilds as $shld)
					{
						if(($shld['start']<=$str)&&($str<=$shld['end']))	// попадает в экранируемый регион 
						{
							unset($pointbuf[$str]);	// удаляем и переходим к следующей
							break;
						}
					}
				}								
								
				ksort($pointbuf);									
				
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
				
				// основной цикл формирования дерева 
				$curr_node = $root;
				$last_pos = 0;
				foreach ($pointbuf as $pos => $point)
				{
					if($point['type']=='open')
					{
						$substr = substr($params['code'],$last_pos,$pos-$last_pos);
						$this->delete_shilds($params,$substr);
						
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
						
						$substr_node = new tn_text($substr);
						
						$curr_node->add_item($substr_node);
						
						$curr_node->_POS_END = $pos;
						$curr_node->_POS_END_END = $pos+strlen($point['buf'][0]);
						
						$last_pos = $curr_node->_POS_END_END;
						
						$curr_node->_END_TAG_REGEXP_RESULT = $point['buf'];
						
						// запустить событие при окончании создания узла
						if(isset($params['onnoderady']))
						{
							$params['onnoderady']($curr_node);
						}
						
						$curr_node = $curr_node->_PARENT;						
						
					}
				}
				
				$substr = substr($params['code'],$last_pos,strlen($params['code'])-$last_pos);
				$this->delete_shilds($params,$substr);
				
				$substr_node = new tn_text($substr);
					
				$pos = strlen($params['code']);
				$root->add_item($substr_node); // добавить айтем
				
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
	
/* Иерархический нумератор */	
	class HNnumerator{
		VAR $buf=[];
		VAR $idx=0;
		VAR $delimeter='.';
		
		function __construct($del='.')
		{
			$this->buf[]=[1];
			$this->delimeter;
		}
		
		function inc()
		{
			$this->buf[$this->idx]++;
		}
		
		function up()
		{
			if($this->idx>=0)
			{
				$this->buf[$this->idx];
				$this->idx--;
			}
		}
		
		function down()
		{
			$this->buf[]=1;
			$this->idx++;
		}
		
		function getText()
		{
			return implode($this->delimeter,$this->buf);
		}
	}
/*
Элемент дерева, строимого парсером
*/
	class treep_node 
	{
		VAR $number=NULL;
		VAR $_IS_TEXT=FALSE;
		
		function is_text()
		{
			return $this->_IS_TEXT;
		}
	}
	
	class tn_text extends treep_node
	{
		VAR $_TEXT;
		
		function __construct($_text_)
		{
			$this->_TEXT=$_text_;
			$this->_IS_TEXT = true;
		}
		
		function text()
		{
			return $this->_TEXT;
		}
					
	}
	
	
	class tn_object extends treep_node
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
		// ходим по дереву, выполняя на каждом узле процедуру вида function($node){}
		function walk($onwalk_callback)
		{
			$onwalk_callback($this);
			foreach($this->_ITEMS as $idx => $item)
			{
				if(is_object($item))
				{
					if(get_class($item)==get_class($this))
					{
						$item->walk($onwalk_callback);
					}
					else
					{
						$onwalk_callback($item);
					}
				}
				else 
				{
					$onwalk_callback($item);
				}
				
			}
		}
		
		
	}
}