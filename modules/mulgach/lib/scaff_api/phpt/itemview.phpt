
<h3>#{View {TABLE_UC}} <#=${table}->getView()#></h3>
<table>

<?php 
foreach($fields as $fld => $fldinfo)
{
	
	if($fld!=$fld_primary)
	{
	?>
	<tr>
	<th valign="top"><label>#{{table}.<?=$fld?>}</label></th>
	<td valign="top">
	<?php
	if(isset($settings['constraints'][$fld]))
	{
		
		?>
		<#=${table}->getField('<?=$fld?>')->getView() #>	
		<?php
	}
	
	elseif($fldinfo['Type']=='enum')
		{
			?><#=${table}->getField('<?=$fld?>',true)#><?php		
		}
	elseif($fldinfo['Type']=='set')
		{
			?><#=implode(${table}->getField('<?=$fld?>'),',') #><?php	
		}
	elseif($fld==$fld_passw)
		{
			
		}
	elseif(($fldinfo['Type']=='longtext') || (stristr($fldinfo['Type'],"varchar")))
		{
			?>
			<p><#=nl2br(${table}->getField('<?=$fld?>',true)) #></p><?php	
		}
	else
		{
			?><#=${table}->getField('<?=$fld?>',true) #><?php	
		}
	
	?>
	</td>
	</tr>
	<?php
	}
}
?>
</table>