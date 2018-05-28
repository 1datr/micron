<?php
namespace modules\base\html{
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
		 $this->call_event('onbody', [],['base.html.js'])
		 ?>
		 </body>
		 </html>
		 <?php 	
		}

	}	
}