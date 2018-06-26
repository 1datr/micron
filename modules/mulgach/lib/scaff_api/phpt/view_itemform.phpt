<#php 
use BootstrapCombobox\ComboboxWidget as ComboboxWidget;
#>
<#php
$form = new mulForm(as_url("{table}/save"),$this);
#>
<input type="hidden" name="{table}[{fld_primary}]" value="<#=((!empty(${table})) ? ${table}->getField('{fld_primary}') : '')#>" />
<h3><#php 
if(${table}->_EXISTS_IN_DB)   
{
	#>
	#{Edit {TABLE_UC}} <#=${table}->getView()#>
	<#php
}
else
{
	#>
	#{Create {TABLE_UC}}
	<#php
}
#></h3>
<table>

<?php 
foreach($fields as $fld => $fldinfo)
{
	
	if($fld!=$fld_primary)
	{
	?>
	<tr>
	<th><label>#{{table}.<?=$fld?>}</label></th><td>
	<?php
	if(!empty($settings['constraints'][$fld]))
	{
		
		?>
		<#php 
		<?php
		if($settings['constraints'][$fld]['required'])
		{
			?>
			$params = array('ds'=> $this->get_controller('<?=$settings['constraints'][$fld]['model']?>')->_MODEL->find() ,'required'=>true, 'name'=>'{table}[<?=$fld?>]');
			<?php
		}
		else
		{
			?>
			$params = array('ds'=> $this->get_controller('<?=$settings['constraints'][$fld]['model']?>')->_MODEL->find() ,'name'=>'{table}[<?=$fld?>]');			
			<?php
		}
		?>
		if(!empty(${table}))
		{
			$params['value']=${table}->getField('<?=$fld?>',true);
		}
		$this->usewidget(new ComboboxWidget(),$params);
		#>
		<div class="error" id='err_<?=$fld?>' role="alert"></div>
		<?php
		}
		elseif($fldinfo['Type']=='enum')
		{
			?><#php $form->field(${table},'<?=$fld?>')->ComboBox();	 #><?php		
		}
		elseif($fldinfo['Type']=='set')
		{
			?><#php $form->field(${table},'<?=$fld?>')->set();	 #><?php	
		}
		elseif($fld==$fld_passw)
		{
			?><#php $form->field(${table},'<?=$fld?>')->password();	 #><?php	
		}
		elseif(isset($settings['file_fields'][$fld]))
		{
			?><#php $form->field(${table},'<?=$fld?>')->file();	 #><?php
		}
		elseif(($fldinfo['Type']=='longtext') || (stristr($fldinfo['Type'],"varchar")))
		{
			?><#php $form->field(${table},'<?=$fld?>')->textarea();	 #><?php	
		}
		else
		{
			?><#php $form->field(${table},'<?=$fld?>')->text();	 #><?php	
		}
	?>
	</td>
	</tr>
	<?php
	}
}
?>
</table>
<input type="hidden" name="back_url" value="<#=$_SERVER['HTTP_REFERER']; #>" />
<#php $form->submit('#{SAVE}'); #>
</form>