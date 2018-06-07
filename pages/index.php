<h2>PAGE1</h2>
<?php $mod_html->set_title("MainPage"); ?>
<p>22322 2 13 21</p>
<?php 
$_code = file_get_contents('./pages/man/1.php');
//$_code = file_get_contents('./pages/man/2.php');

//$this->MLAM->_call_module('base.modutil','create_module',['modname'=>'base.php.managed']);

$this->MLAM->_call_module('base.php.managed','compile',['code'=>$_code]);
?>