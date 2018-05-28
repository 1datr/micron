<?php
function merge_arrays($array1,$array2)
{
	$res = $array1;
	if(!empty($array2))
	{
		foreach ($array2 as $key => $val)
		{
			if(!in_array($val,$res))
			{
				$res[]=$val;
			}
		}
	}
	return $res;
}

function merge_arrays_assoc()
{
	$numargs = func_num_args();
	$arg_list = func_get_args();
	$_merge_if_not_exists=true;
	if(is_bool($args_list[$numargs-1]))
	{
		$_merge_if_not_exists = $args_list[$numargs-1];
	}
	$res=array();
	foreach ($arg_list as $idx => $arg_array)
	{
		
		foreach ($arg_array as $key => $val)
		{
			if($_merge_if_not_exists)
			{
			/*	if( (!is_object($val)) && (!is_array($val)) )
				{*/
					//if(!isset($res[$val]))  
					if(!key_exists($key, $res))
					{
						$res[$key]=$val;
					}
			//	}
			}
			else 
			{
				$res[$key]=$val;
			}
		}
	}
	return $res;
}

function ser_post($ser_name)
{
	GLOBAL $_BASEDIR;
	$ser_dir = url_seg_add($_BASEDIR,'/test/post_ser');
	$path=url_seg_add($ser_dir,"{$ser_name}.ser");
	file_put_contents($path, serialize($_POST));
}

function get_ser($ser_name)
{
	GLOBAL $_BASEDIR;
	$ser_dir = url_seg_add($_BASEDIR,'/test/post_ser');
	$path=url_seg_add($ser_dir,"{$ser_name}.ser");
	$ser_code = file_get_contents($path);
	return unserialize($ser_code);
}

function assoc_array_cut($assoc_arr,$_KEY)
{
	$res=array();
	foreach ($assoc_arr as $idx => $val)
	{
		if(is_object($val))
		{
			if(property_exists($val,$_KEY))
				$res[$idx]=$val->$_KEY;
		}
		elseif(is_array($val))
		{
			if(isset($val[$_KEY])) $res[$idx]=$val[$_KEY];
		}
	}
	return $res;
}

function array_order_num($arr)
{
	$pos=0;
	$newarray=array();
	foreach($arr as $idx => $val)
	{
		if(is_int($idx))
		{
			$newarray[$pos]=$val;
			$pos++;
		}
		else
			$newarray[$idx]=$val;
	}
	return $newarray;
}

function array_insert(&$array, $position, $insert)
{
	if (is_int($position)) {
		array_splice($array, $position, 0, $insert);
	} else {
		$pos   = array_search($position, array_keys($array));
		$array = array_merge(
				array_slice($array, 0, $pos),
				$insert,
				array_slice($array, $pos)
				);
	}
}

function x_array_push($arr,$newitem)
{
	if(is_array($newitem))
	{
		return merge_arrays($arr, $newitem);
	}
	array_push($arr, $newitem);
	return $arr;
}

function _array_diff($arrA,$arrB)
{
	$newarray=array();
	foreach ($arrA as $El_A)
	{
		if(!in_array($El_A, $arrB))
		{
			$newarray[]=$El_A;
		}
	}
	return $newarray;
}

function def_options($defs,&$opt_array)
{
	foreach ($defs as $defkey => $defval)
	{
		if(!isset($opt_array[$defkey]))
			$opt_array[$defkey]=$defval;
	}
}


function ximplode($delimeter,$array,$prefix,$suffix,$options=NULL)
{
	$i=0;
	$str = "";
	foreach($array as $key => $item)
	{
		$itemz = $item;
		$prefixz=strtr($prefix,array('{value}'=>$item,'{key}'=>$key));
		$suffixz=strtr($suffix,array('{value}'=>$item,'{key}'=>$key));
		$delimeterz=strtr($delimeter,array('{value}'=>$item,'{key}'=>$key));
		if($i>0)
		{
			$str = $str.$delimeterz;
		}
		$str=$str.$prefixz.$item.$suffixz;
		$i++;
	}
	return $str;
}
/*
 *    $arr - массив, возможно ассоциативный, возможно структур
 * 	  $delimeter - разделитель
 *    $template - шаблон. ключи - индексы из струтуры, специальные ключи - {%val} - значение (если не структурами) {idx} - индекс
 *    $onelement - событие с параметрами &$theval,&$idx,&$thetemplate,&$ctr,$thedelimeter
 * */
function xx_implode($arr,$delimeter,$template,$onelement=NULL)
{
	$ctr=0;
	$str="";
	foreach ($arr as $idx => $val)
	{
		$thetemplate = $template;
		$thedelimeter = $delimeter;
		
		if(!is_array($val))
		{			
			$theval["%val"]=$val;
		}
		else 
		{
			$theval=$val;
			
		}
		if($onelement!=NULL)
		{
			
			$onelement($theval,$idx,$thetemplate,$ctr,$thedelimeter);
		}
		$theval["idx"]=$idx;
		
	//	print_r($theval);
		
		$newstr = x_make_str($thetemplate,$theval);
		if($ctr>0)
			$str=$str.$thedelimeter.$newstr;
		else 
			$str=$newstr;
		$ctr++;
	}
	return $str;
}

function x_array_walk(&$arr,$onelement)
{
	foreach($arr as $idx => $val)
	{
		$return = false;
		$onelement($idx,$val,$return);
		if($return)
			return;
	}
}

function get_by_key_case_no_sensitive($hash,$key)
{
	foreach ($hash as $_key => $val)
	{
		if(strtolower($key)===strtolower($_key))
			return $val;
	}
	return NULL;
}

function x_make_str($str,$ptrn)
{
	$ptrn2=array("{%0}"=>$ptrn);
	if(is_array($ptrn))
	{
		foreach ($ptrn as $key => $val)
		{
			$ptrn2["{".$key."}"]= (string)$val;
		}
	}
	elseif(is_object($ptrn))
	{
		$vars = get_class_vars($ptrn);
		foreach ($vars as $key => $val)
		{
			$ptrn2["{".$key."}"]=$val;
		}
	}
	else 
	{
		$ptrn=array('%val'=>$ptrn);
	}
	$str_res = strtr($str,$ptrn2);
	
	$str_res = exe_php_str($str_res,$ptrn);
	//mul_dbg($res);
	return $str_res;
}

function exe_php_str($code_str,$addition_vars=array())
{
	//mul_dbg(debug_backtrace(),false);
	
	foreach ($addition_vars as $var => $val)
	{
		$$var=$val;
	}
	
	ob_start();
	$code_str = "echo ''; ?>{$code_str}<? echo '';";
	eval($code_str);
	$res = ob_get_clean();
	return $res;
}

function delete_from_array_by_value($val, &$arr)
{
	unset($arr[array_search($val,$arr)]);
}

function url_seg_add()
{
	$numargs = func_num_args();
	$arg_list = func_get_args();
	$resstr="";
	$flg_backslash=true;
	if(is_bool($arg_list[$numargs-1]))
	{
		$flg_backslash=$arg_list[$numargs-1];
	}
	foreach ($arg_list as $idx => $arg)
	{
		if((substr($arg,-1)=="/") || (substr($arg,-1)=="\\"))
		{
			$arg = substr($arg,0,-1);
		}
		
		if((substr($arg,0,1)=="/") || (substr($arg,0,1)=="\\") )
		{
			$arg = substr($arg,1,strlen($arg)-1);
		}
		
		if($flg_backslash)
			$arg = strtr($arg,['\\'=>'/']);
		
		if($idx==0)
		{
			$resstr=$arg;
		}
		else 
		{
			$resstr=$resstr."/".$arg;
		}
	}
	
	$resstr = strtr($resstr,array('//'=>'/'));

	//mul_dbg($arg_list);
	
	if(substr($arg_list[0],0,1)=='/')
	{
		if(substr($resstr[0],0,1)!='/')
		$resstr="/{$resstr}";
	}
	return $resstr;
	
}



// создать файл рекурсивно полностью
function x_file_put_contents($filename,$data,$flags=0,$context=null)
{
	$parent_path = dirname($filename);
	if(!file_exists($parent_path))
	{
		x_mkdir($parent_path);
	}
	file_put_contents($filename, $data,$flags,$context);
}

function file_put_contents_ifne($filename,$data,$flags=0,$context=null)
{
	if(!file_exists($filename))
		x_file_put_contents($filename, $data,$flags,$context);
}
// создать папку рекурсивно полностью
function x_mkdir($path)
{
//	mul_dbg("creating dir ".$path);
	$parent_path = dirname($path);
	if(file_exists($parent_path))
	{		
		if(!file_exists($path))
		{
			mkdir($path);
			
		}
	}
	else 
	{
		x_mkdir($parent_path);
		mkdir($path);
	}
}

function mul_dbg($var,$print_r=true)
{
	GLOBAL $_MUL_DBG_WORK;
	if($_MUL_DBG_WORK)
	{
		$file_dbg = url_seg_add(__DIR__,'debug.txt');
		
		if(is_string($var))
		{
			$newstr=$var;
		}
		else 
		{
			ob_start();
			if($print_r)
				print_r($var);
			else
				var_dump($var);
			$newstr = ob_get_clean();
		}
		
		$content="";
		if(file_exists($file_dbg))
		{
			$content = file_get_contents($file_dbg);
		}
		$content=$content."
				
	".date("m-d-Y H:i:s.u").": {$newstr}";
		
		x_file_put_contents($file_dbg, $content);
	}
}
// добавить точку перед директорией 
function dir_dotted($dir)
{
	if((substr($dir,0,2)=='./') || (substr($dir,0,3)=='../'))
	{
		return $dir;
	}
	return url_seg_add('./', $dir);
}
/*
 * 	$array1 - массив	
 *  $ev_onelement - функция function(&$element) return true or false $element - массив с ключами index и value
 * */
function filter_array($array1,$ev_onelement)
{
	$res = array();
	foreach ($array1 as $idx => $val)
	{
		$element_res=array('index'=>$idx,'value'=>$val);
		$e_res = $ev_onelement($element_res);
		if($e_res)
		{
			$res[$element_res['index']]=$element_res['value'];	
		}
	}
	return $res;
}

function set_back_page($_URL)
{
	if($_SESSION['back_page']['change'])
		$_SESSION['back_page']['url']=$_URL;
}

function _redirect($_url)
{
	
	?>
	<script language="javascript">
		document.location = "<?=$_url?>";
	</script>
	<?php 
}

function convert_slash($url)
{
	return strtr($url,array('\\'=>'/'));
}

function get_files_in_folder($dir_path,$opts=array())
{
	def_options(array('dirs'=>false,'basename'=>false,'without_ext'=>false), $opts);
	$d = dir(convert_slash($dir_path));
	$result=array();
	//	echo "Дескриптор: " . $d->handle . "\n";
	//	echo "Путь: " . $d->path . "\n";
	while (false !== ($entry = $d->read())) {
		if(($entry!="..")&&($entry!="."))
		{
			$filename = url_seg_add($dir_path, $entry);
			if(count($opts)==0)
			{
			}
			else 
			{
				if($opts['dirs'])
				{
					if(!is_dir($filename))
					{
						continue;
					}
				}				
			}
			if($opts['basename'])
				$result[]=basename($filename);
			elseif($opts['without_ext'])
			{
				$info = pathinfo($filename);
				$result[]=basename($filename,'.'.$info['extension']);
			}
			else
				$result[]=$filename;
					
		}
	}
	$d->close();
	return $result;
}

function get_nested_dirs($the_dir)
{
	$filelist = get_files_in_folder($the_dir);
	$the_dirs=array();
	foreach ($filelist as $the_file)
	{
		if(is_dir($the_file))
		{
			$the_dirs[]=$the_file;
		}
	}
	return $the_dirs;
}

function GenRandStr($length=6,$space=false) {

	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
	if($space) $chars=$chars." ";

	$code = "";

	$clen = strlen($chars) - 1;
	while (strlen($code) < $length) {

		$code .= $chars[mt_rand(0,$clen)];
	}

	return $code;

}
// get equal if exists
function eql_ife($arr,$key,$val)
{
	if(isset($arr[$key]))
	{
		return ($arr[$key]==$val);
	}
	return false;
}

function calc_ife($arr,$key,$calc_func)
{
	if(isset($arr[$key]))
	{
		return $calc_func($arr[$key]);
	}
	return false;
}

function string_diff($str1,$str2)
{
	return strtr($str1,array($str2=>''));
}

function filepath2url($path)
{
	global $_BASEDIR;
	$str = url_seg_add($_BASEDIR,string_diff( strtr($path,array('\\'=>'/')), strtr($_SERVER['DOCUMENT_ROOT'],array('\\'=>'/')) ));	
	return as_url($str);
}

function array_to_pages($the_array,$pagesize=7)
{
	$res=array();
	
	return $res;
}

function as_uri($str)
{
	return url_seg_add('/', $str);
}

function as_url($str)
{
			
	$script_path = realpath($_SERVER['SCRIPT_NAME']);
	//mul_dbg($_SERVER['SERVER_NAME']);
	
	return url_seg_add('/', dirname($_SERVER['SCRIPT_NAME']), $str);
}

function unlink_folder($fldr)
{
	$nested_files=get_files_in_folder($fldr);
	foreach ($nested_files as $nested)
	{
		if(is_dir($nested))
		{
			unlink_folder(dir_dotted($nested));
		}
		else 
		{
			//chown($nested, 666);
			if(is_dir($nested))
				rmdir($nested);
			else
				unlink(dir_dotted($nested));
		}
	}
	chown($fldr, 666);
	//if(file_exists($fldr)) echo ";;;";
	if(is_dir($fldr))
		rmdir($fldr);
	else
		unlink($fldr);
}

function add_keypair(&$arr,$key,$val)
{
	if(empty($arr[$key]))
	{
		$arr[$key]=array();		
	}
	$arr[$key][]=$val;
}
// Найти файл
function find_file($search, $dir_path=".", $rootonly=FALSE)
{
	if(!file_exists($dir_path))
	{
		return array();
	}
	$d = dir($dir_path);
	$result=array();
//	echo "Дескриптор: " . $d->handle . "\n";
//	echo "Путь: " . $d->path . "\n";
	while (false !== ($entry = $d->read())) {
		if(($entry!="..")&&($entry!="."))
		{			
			$filename = url_seg_add($dir_path, $entry);
			if($entry==$search)
			{
				$result[]=$filename;
			}
			
			if($rootonly==FALSE)
			{
				if(is_dir($filename))
				{
					
					$result_nested = find_file($search, $filename);
					$result = array_merge($result,$result_nested);
				}
			}
		}
	}
	$d->close();
	return $result;
}

function parse_code_template($tpl_file,$var_array)
{
	foreach ($var_array as $var => $val)
	{
		$$var=$val;
	}

	ob_start();
	if(file_exists($tpl_file))
		include $tpl_file;
	
	$code = ob_get_clean();
		// php tags
	$code = strtr($code,array('<#'=>'<?','#>'=>'?>'));

	$var_array2=array();
	foreach ($var_array as $var => $val)
		{
			$var_array2['{'.$var.'}']=$val;
		}
	return strtr($code,$var_array2);
}

function UcaseFirst($str)
{
	return ucfirst(strtolower($str));
}
//function 
