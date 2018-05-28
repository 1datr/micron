<?php
namespace modules\html{
use Core;

class Module extends Core\Module 
	{	
	
		function core_onload()
		{
		 ?>
		 <html>
		 <head>
		 <?php 
		 $this->call_event('onhead', [])
		 ?>
		 </head>
		 <body>
		 <?php 
		 $this->call_event('onbody', [])
		 ?>
		 </body>
		 </html>
		 <?php 	
		}

	}	
}