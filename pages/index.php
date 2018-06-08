<h2>PAGE1</h2>
<?php $mod_html->set_title("MainPage"); ?>
<p>22322 2 13 21</p>
<?php 
$_code = file_get_contents('./pages/man/1.php');
//$_code = file_get_contents('./pages/man/2.php');

//$this->MLAM->_call_module('base.modutil','create_module',['modname'=>'treep']);

$_code = "
xxx		
		{#if c=\"xx\" (x==0)
		{#then 
				x=x+1;
			#}
		{#else 
				x=x+2;
			#}
		#}
xxx
		{#foreach(arr_x as idx => x)

		#}";
$tree = $this->MLAM->_call_module('treep','compile',[
		'code'=>$_code,
		'nstart'=>'/\{#([[:alnum:]]+)(\s+([[:alnum:]]+)=\\"([[:alnum:]]+)\\")*/s',
		'nend'=>'/#\}/'		
]);
if($tree==null)
{
	echo "<h3>".$this->MLAM->_call_module('treep','get_err_text',[])."</h3>";
}
print_r($tree);
?>