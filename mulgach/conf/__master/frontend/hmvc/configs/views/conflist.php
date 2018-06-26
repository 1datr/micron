<table>
<?php
//print_r($files);
foreach ($files as $the_file)
{
	if(!is_dir($the_file))
	{
		continue;
	}
	?>
	<tr>
	<td><a href="<?=as_url('hmvc/'.basename($the_file))?>"><?=basename($the_file)?></a></td>
	
	
	</tr>
	<?php 
}
?>
</table>
