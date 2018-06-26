<?php
namespace modules\mulgach{
use Core;
	
	class HMVCRequest
	{
		VAR $_controller='site';
		VAR $_action='index';
		VAR $_args=array();
		VAR $_real_args=array();
		VAR $_fun_map=array();
		VAR $_URL_FORMAT='folder_like';
	
		function __construct($req_str,$action=NULL,$args=NULL,$_URL_FORMAT='folder_like')
		{
			$this->_URL_FORMAT = $_URL_FORMAT;
			if(($action==NULL)&&($args==NULL))
			{
				$this->from_str($req_str);
	
			}
			else
			{
				$this->_controller=$req_str;
				$this->_action=$action;
				$this->_args=$args;
			}
	
		}
	
		function save_args_as_real()
		{
			$this->_real_args = $this->_args;
		}
		// получить аргумент
		function getArg($key)
		{
			if(isset($this->_args[$key]))
				return $this->_args[$key];
				else
					return null;
	
		}
	
		function from_str($req_str)
		{
			$_result=array();
	
			$def_controller='site';
			$def_action='index';
			$req_slices = explode('/', $req_str);
			$argidx=0;
			$str_varval="";
	
			$req_pieces=array();
			foreach ($req_slices as $idx => $slice)
			{
				if(empty($slice))
					continue;
					list($varname,$varval)=explode(':',$slice);
					if(empty($varval))
					{
						$req_pieces[]=array('value'=>$varname);
					}
					else
					{
						$req_pieces[$varname]=array('value'=>$varval);
					}
			}
	
			if(key_exists('controller',$req_pieces))
			{
				$this->_controller = $req_pieces['controller']['value'];
				unset($req_pieces['controller']);
			}
			else
			{
				if(!empty($req_pieces[0]))
				{
					$this->_controller = $req_pieces[0]['value'];
					unset($req_pieces[0]);
				}
			}
	
			if(key_exists('action',$req_pieces))
			{
				$this->_action= $req_pieces['action']['value'];
				unset($req_pieces['action']);
			}
			else
			{
				if(!empty($req_pieces[1]))
				{
					$this->_action = $req_pieces[1]['value'];
					unset($req_pieces[1]);
				}
			}
	
			$strparams='';
			foreach($req_pieces as $idx => $val)
			{
				if(is_string($idx))
				{
					$val=$val['value'];
					if($strparams=='')
						$strparams="$idx=$val";
						else
							$strparams=$strparams."&{$idx}={$val}";
							unset($req_pieces[$idx]);
				}
				else
				{
					$_result[$idx]=$val['value'];
				}
			}
	
	
			if(!empty($strparams))
			{
				$parsed = array();
				parse_str($strparams,$parsed);
				$_result= array_merge($_result,$parsed);
				//	var_dump($_result);
			}
			else
			{
				$_result = array();
				foreach ($req_pieces as $idx => $piece)
				{
					$_result[]=$piece['value'];
				}
				//$_result = $req_pieces;
			}
	
	
			$this->_args = array_order_num($_result);
			//
			if(isset($_REQUEST['args']))
			{
				$this->_args = $_REQUEST['args'];
				unset($_REQUEST['args']);
			}
			//mul_dbg($this->_args);
		}
	
		function get_array_url($varname,$arr)
		{
			$str_res="";
			foreach ($arr as $key => $val)
			{
				if(is_array($val))
				{
	
					$str_res=$str_res."/".$this->get_array_url($varname.'['.$key.']',$val);
				}
				else
				{
					$str_res=$str_res."/".$varname.'['.$key.']:'.$val;
				}
			}
			return $str_res;
		}
		// полный url
		function get_ref($real_url=true)
		{
			$res_url="/".$this->_controller;
			if($this->_action!='index')
				$res_url = $res_url."/".$this->_action;
	
				$arr_args=array();
	
				if($real_url)
					$_args = $this->_real_args;
					else
						$_args = $this->_args;
						if(is_array($ref_map))
						{
								
							/*	$_args_int = array();
							 $_args_str = array();
							 foreach ($ref_map as $key => $_val)
							 {
							 if(is_int($key))
							 {
							 $_args_int[$key]=$val;
							 }
							 else
							 {
							 if($k)
							 	$_args_int[$key]=$val;
							 	}
							 	}*/
						}
	
						foreach ($_args as $idx => $argval)
						{
							if(is_string($idx))
							{
								if(is_array($argval))
								{
									$arr_args[$idx]=$argval;
								}
								else
								{
									$res_url=$res_url."/".$idx.":".$argval;
								}
							}
							else
							{
								$res_url=$res_url."/".$argval;
							}
						}
							
						foreach ($arr_args as $_key => $arg_val )
						{
							$res_url = $res_url.$this->get_array_url($_key,$arg_val);
						}
	
						return $res_url;
		}
	
		function setmap($map)
		{
			$this->_fun_map=$map;
		}
	
		function get_alternative()
		{
			$newargs=$this->_args;
			array_insert($newargs, 0, array($this->_action));
			$newreq = new HMVCRequest($this->_controller,'index',$newargs);
			return $newreq;
		}
	
		function delete_arg($argidx)
		{
			if(!empty($this->_args[$argidx]))
			{
				unset($this->_args[$argidx]);
			}
		}
	
		function make_url($url_content)
		{
			switch($this->_URL_FORMAT)
			{
				case 'folder_like':
					return as_url($url_content);
					break;
				case 'oldschool_url':
					return "?r=".$url_content;
					break;
						
			}
			return "";
			//'_URL_FORMAT'=>'folder_like'
		}
	
		function url_modified($args_change,$args_delete=array(),$_controller=null,$_action=null)
		{
			if($_controller!=null)
			{
				$newstr=$_controller;
			}
			else
				$newstr=$this->_controller;
				if($this->_action!='index')
				{
					$newstr=url_seg_add($newstr,$this->_action);
				}
				else
				{
					$newstr=url_seg_add($newstr,$_action);
				}
	
				$newargs=array();
				foreach ($this->_args as $idx => $arg)
				{
					if(!empty($args_delete[$idx]))
						continue;
						if(!empty($args_change[$idx]))
							$newargs[$idx] = $args_change[$idx];
							else
								$newargs[$idx] = $arg;
				}
	
				//print_r($newargs);
	
				foreach ($this->_fun_map as $fmidx => $fld)
				{
					$newstr=url_seg_add($newstr,$newargs[$fld]);
					unset($newargs[$fld]);
				}
	
				foreach ($newargs as $argidx => $argval)
				{
					if(is_int($argidx))
					{
						$newstr=url_seg_add($newstr,$argval);
					}
					else
					{
						$newstr=url_seg_add($newstr,$argidx.":".$argval);
					}
				}
	
				return $this->make_url($newstr);
		}
	}

}