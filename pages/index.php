<h2>PAGE1</h2>
<?php $mod_html->set_title("MainPage"); ?>
<p>22322 2 13 21</p>
<?php 
$_code = file_get_contents('./pages/man/1.php');
//$_code = file_get_contents('./pages/man/2.php');

//$this->MLAM->_call_module('base.modutil','create_module',['modname'=>'base.tree']);
$res = $this->MLAM->_call_module('base.php.managed','compile',['code'=>$_code]);
/*
$_code = "
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
ddd";*/
		/*
$tree = $this->MLAM->_call_module('treep','compile',[
		'code'=>$_code,
	/#	'nstart'=>'/\{\#([[:alnum:]]+)/',
		'nend'=>'/\#\}/',
		'comments'=>['#\/\#.*\#\/#s','#\/\/.*$#m'],
		'shields'=>[['{@','@}']],#/		
		'nstart'=>'/((while|for|foreach|if|elseif|switch|case)\((.+)\).*$\s*\{)|((while|for|foreach|if|elseif|switch)\((.+)\).*\s*\{)|(else\s*\{)/',
		'nend'=>'/\}/',
		'comments'=>['#\/\*.*\*\/#Us','#\/\/.*$#m'],
		'shields'=>[['\?>','<\?php','clear'=>false],['\?>','<\?=','clear'=>false],['\?>','$','clear'=>false]],
		
]);
if($tree==null)
{
	echo "<h3>".$this->MLAM->_call_module('treep','get_err_text',[])."</h3>";
}
else
{
	$tree->walk(function($item)
	{
		if($item->is_text())
			echo $item->text();
		else 
		{
			print_r($item->_START_TAG_REGEXP_RESULT);
			print_r($item->_END_TAG_REGEXP_RESULT);
		}
	});
}
*/
	eval("/* ddd */
\$z=7;
// dede
?><h3>123</h3><?php
\$x=8;");
//print_r($tree);
?>