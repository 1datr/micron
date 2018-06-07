<?php
$fruits = ['pie','apple','cherry'];	

foreach($fruits as $fruit)
{
	echo "<li>";		
	echo $fruit;
		for($i=0;$i<5;$i++)
		{
			echo "<li>$i</li>";
		}
	echo "</ul>";
	echo "</li>";	
}