<h2>PAGE1</h2>
<?php $mod_html->set_title("MainPage"); ?>
<p>22322 2 13 21</p>
<?php 
$_code = file_get_contents('./pages/man/1.php');
//$_code = file_get_contents('./pages/man/2.php');

//$this->MLAM->_call_module('base.modutil','create_module',['modname'=>'treep']);
//$this->MLAM->_call_module('base.php.managed','compile',['code'=>$_code]);

$_code = "
/* {#rz
		*/		
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
ddd";
$tree = $this->MLAM->_call_module('treep','compile',[
		'code'=>$_code,
		'nstart'=>'/\{\#([[:alnum:]]+)/',
		'nend'=>'/\#\}/',
		'comments'=>['#\/\*.*\*\/#s','#\/\/.*$#'],
		/*
		'nstart'=>'/((while|for|foreach|if|else|elseif|switch)\((.+)\).*$\s*\{)|((while|for|foreach|if|else|elseif|switch)\((.+)\).*\s*\{)/',
		'nend'=>'/\}/'*/		
]);
if($tree==null)
{
	echo "<h3>".$this->MLAM->_call_module('treep','get_err_text',[])."</h3>";
}
else
{
	$tree->walk(function($item)
	{
		if(is_string($item))
			echo $item;
		else 
		{
			print_r($item->_START_TAG_REGEXP_RESULT);
			print_r($item->_END_TAG_REGEXP_RESULT);
		}
	});
}

	eval("/* ddd */
\$z=7;
// dede
?><h3>123</h3><?php
\$x=8;");
//print_r($tree);
?>