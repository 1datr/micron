<#php
$form = new \mulForm(as_url('{makeuser_action}'),$this);
$captcha = mul_captcha::use_captcha($this,['model'=>&$reg_form_struct,'form'=>&$form]);
//var_dump($reg_struct);
#>
<table>
<?php 
foreach($fields_ordered as $fld => $fldinfo)
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
			?><#php $form->field($reg_struct,'<?=$fld?>')->ComboBox();	 #><?php		
		}
		elseif($fldinfo['Type']=='set')
		{
			?><#php $form->field($reg_struct,'<?=$fld?>')->set();	 #><?php	
		}
		elseif(($fld==$fld_passw) || ($fld==$fld_passw_re))
		{
			?><#php $form->field($reg_struct,'<?=$fld?>')->password();	 #><?php	
		}
		elseif(isset($settings['file_fields'][$fld]))
		{
			?><#php $form->field($reg_struct,'<?=$fld?>')->file();	 #><?php
		}
		elseif(($fldinfo['Type']=='longtext') || (stristr($fldinfo['Type'],"varchar")))
		{
			?><#php $form->field($reg_struct,'<?=$fld?>')->textarea();	 #><?php	
		}
		else
		{
			?><#php $form->field($reg_struct,'<?=$fld?>')->text();	 #><?php	
		}
	?>
	</td>
	</tr>
	<?php
	}
}
?>
  <tr> 	  
    <td rowspan="2">#{CAPTCHA_CAPTION}</td>
    <td>
    <# $captcha->full_html($form,$reg_struct);  #>
    </td>
  </tr>

</table>

<#php
$form->submit('#{REGISTER}');
$form->close();
#>