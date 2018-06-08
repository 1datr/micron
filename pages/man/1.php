<?php
// fruits array
$fruits = ['pie','apple','cherry'];
?>
<h5>foreach(@@){}</h5>
<ul>		
<?php
/* +=+=+=+
 *  */
foreach($fruits as $fruit)
{
	?><li>		
	<?=$fruit?>
		<ul>
		<?php
		for($i=0;$i<5;$i++)
		{
			echo "<li>$i</li>";
		}
		?>
		</ul>
	</li>	
	<?php
}
?>
</ul>
<h6>foreach2</h2>
<?php
$labels = ['L1','L2'];
?>
<ul>		
<?php
foreach($labels as $label)
{
	?><li><?=$label?></li><?
}		
?>
</ul>