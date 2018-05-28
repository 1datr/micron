<?php
namespace modules\base\html{
use Core;

class Module extends Core\Module 
	{	
	
		VAR $_HTML;
		VAR $_HEADINFO;
		function core_onload()
		{
			$this->grub_page();
		 	?>
 <html>
 <head> 			
<?php 	$this->call_event('beforehead', []); 	?>
<title><?=((isset($this->_HEADINFO['title']))? $this->_HEADINFO['title'] : "")?></title>
<?php 	$this->call_event('afterhead', []); 	?>
</head>
<body>
<?=$this->_HTML?>
</body>
</html>
		 <?php 	
		}
		
		function grub_page()
		{
			ob_start();
			$this->call_event('onbody', ['mod_html'=>$this],['base.html.js']);
			$this->_HTML = ob_get_clean();
		}
		
		function set_title($_title)
		{
			$this->_HEADINFO['title']=$_title;
		}

	}	
}