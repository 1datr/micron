<a href="<?=as_url("/emaker/".$newentity->getField('cfg'))?>">#{Enitity manager}</a>
<?php 
$_TEXT_FOR_FILETYPE="#{For example : }audio/*, image/*";

$form = new mulForm(as_url("/emaker/save"),$this,[],false);
?>
<?php 
$sbplugin->template_table_start('fields_item',['valign'=>"top",'class'=>'fielditem']);
?>
	<td>
	<input type="hidden" name="nameroot" value="entity[fieldlist][{idx}]" />
	<?php $form->field($emptyfld, 'fldname',['namemode'=>'multi','name_ptrn'=>'{idx}'])->text([]);  ?>	
	</td>
	<td><?php $form->field($emptyfld, 'type',['namemode'=>'multi','name_ptrn'=>'{idx}'])->ComboBox($typelist,['htmlattrs'=>['class'=>'fldtype','onchange'=>'on_type_change(this)']]);  ?></td>
	<td><span class="fldinfo"></span></td>
	<td><?php $form->field($emptyfld, 'primary',['namemode'=>'multi','name_ptrn'=>'{idx}'])->checkbox([]);  ?></td>	
	<td><?php $form->field($emptyfld, 'required',['namemode'=>'multi','name_ptrn'=>'{idx}'])->checkbox([]);  ?></td>	
	<td>
	<?php $form->field($emptyfld, 'file',['namemode'=>'multi','name_ptrn'=>'{idx}'])->checkbox(['htmlattrs'=>['onchange'=>'on_sel_file(this)']]);  ?>
	<span class="filetype_div" style="display: none">
	<?php $form->field($emptyfld, 'filetype',['namemode'=>'multi','name_ptrn'=>'{idx}'])->text(['htmlattrs'=>['class'=>'filetype','placeholder'=>'File type']]); ?>
	<div>
	<?=$_TEXT_FOR_FILETYPE?>
	</div>
	</span>
	</td>		
	<td><?php $form->field($emptyfld, 'defval',['namemode'=>'multi','name_ptrn'=>'{idx}'])->text([]);  ?></td>
	<td>
		<a href="javascript:" class="fields_item_move" moveto="-1"  title="#{Move up}">
			<img alt="" src="<?=$this->get_image('../../images/triangle_up.png')?>" width="18px" height="18px" />
		</a>	
	</td>
	<td>
		<a href="javascript:" class="fields_item_move" moveto="1"  title="#{Move down}">
			<img alt="" src="<?=$this->get_image('../../images/triangle_down.png')?>" width="18px" height="18px" />
		</a>	
	</td>
	<td><button type="button" class="fields_item_drop">x</button></td>
<?php 
$sbplugin->template_table_end();
?>

<?php $form->draw_begin(); ?>

<?php $form->field($newentity, 'cfg')->hidden([]);  ?>

<?php $sbplugin->table_block_start('fields_item',array('id'=>'fields_block'),[],"
<tr><th>#{Field name:}</th><th colspan=\"2\">#{Type:}</th><th>#{Primary:}</th><th>#{Required:}</th><th>#{File field:}</th><th>#{Default value:}</th></tr>		
		");?>
<h4>
<?php 
if($mode=='create')
{
	?>#{New entity creation}<?php 
}
else 
{
	?>#{Edit entity }<?=$newentity->getField('ename')?><?php
}
?>
</h4>
<label><?=Lang::__t('Field name:') ?></label>	
<?php $form->field($newentity, 'ename')->text([]);  ?>
<?php $form->field($newentity, 'oldname')->hidden([]);  ?>
<button type="button" class="fields_item_add btn btn-primary btn-sm" target="fields_block" title="#{Add field}">#{ADD FIELD}</button>
<?php 
	foreach ($newentity->getField('fieldlist') as $idx => $fld)
	{
?>
<tr class="fielditem multiform_block" role="item" valign="top" class="fielditem">
	<td>
<?php 
	$af_fldname = $form->field($fld, 'fldname',['namemode'=>'multi','nameidx'=>$idx]);
	$form->field($fld, 'fldname_old',['namemode'=>'multi','nameidx'=>$idx])->hidden([]);
?>
	<?php $af_fldname->text([]);  ?>
	</td>
	<td>	
	<input type="hidden" name="nameroot" value="<?=$af_fldname->get_name_root()."[$idx]"  ?>" />
	<?php $form->field($fld, 'type',['namemode'=>'multi','nameidx'=>$idx])->ComboBox($typelist,['htmlattrs'=>['class'=>'fldtype','onchange'=>'on_type_change(this)']]);  ?></td>
	<td><span class="fldinfo"><?php 
	$fld_typeinfo = $fld->getField('typeinfo');
	if(!empty($fld_typeinfo))
	{
		if($fld_typeinfo->FieldExists('entity_to'))
		{
			?>
	<!-- entity reference -->
	<table class="_eref">
	<tr>
	<td><?php $form->field($fld_typeinfo,'entity_to', [])->ComboBox($elist,['htmlattrs'=>['class'=>'_entity_to','onchange'=>'on_entity_change(this)']]);  ?></td>
	<td><?php $form->field($fld_typeinfo,'fld_to', [])->ComboBox($fld_typeinfo->getField('fieldlist'),['htmlattrs'=>['class'=>'_fld_to','onchange'=>'on_fld_to_change(this)']]);  ?></td>
	</tr>
	</table>
			<?php 
		}
		else
		{
			$fld_typeinfo->draw_def_form($form,['show_labels'=>false,
				'name_root'=>$af_fldname->get_name_root()."[".$idx."][typeinfo]"				
		]);
		}
	}
	?></span></td>
	<td><?php $form->field($fld, 'primary',['namemode'=>'multi','nameidx'=>$idx])->checkbox([]);  ?></td>	
	<td><?php $form->field($fld, 'required',['namemode'=>'multi','nameidx'=>$idx])->checkbox([]);  ?></td>	
	<td><?php 
		if($fld->getField('file_enabled'))
		{
			$form->field($fld, 'file',['namemode'=>'multi','nameidx'=>$idx])->checkbox(['htmlattrs'=>['onchange'=>'on_sel_file(this)']]);
			if(!$fld->getField('file'))
			{
			?>
			<span class="filetype_div" style="display: none">
			<?php $form->field($fld, 'filetype',['namemode'=>'multi','nameidx'=>$idx])->text(['htmlattrs'=>['class'=>'filetype','placeholder'=>'File type']]); ?>
<div><?=$_TEXT_FOR_FILETYPE?></div>
			</span>
			<?php   
			}
			else
			{
			?>	
			<span class="filetype_div" >
			<?php $form->field($fld, 'filetype',['namemode'=>'multi','name_ptrn'=>'{idx}','nameidx'=>$idx])->text(['htmlattrs'=>['class'=>'filetype','placeholder'=>'File type']]); ?>
				<div>
				<?=$_TEXT_FOR_FILETYPE?>
				</div>
			</span>
			<?php 
			}
			
		}
	?></td>	
	<td><?php 
	if($fld->getField('defval_enable'))
	{
		$form->field($fld, 'defval',['namemode'=>'multi','nameidx'=>$idx])->text([]);  
	}
	?>
	</td>	
	<td>
		<a href="javascript:" class="fields_item_move" moveto="-1"  title="#{Move up}">
			<img alt="" src="<?=$this->get_image('../../images/triangle_up.png')?>" width="18px" height="18px" />
		</a>	
	</td>
	<td>
		<a href="javascript:" class="fields_item_move" moveto="1"  title="#{Move down}">
			<img alt="" src="<?=$this->get_image('../../images/triangle_down.png')?>" width="18px" height="18px" />
		</a>	
	</td>
	<?php 
	if($fld->getField('deletable'))
		{
	?>
	<td><button type="button" class="fields_item_drop">x</button></td>
	<?php 
		}
	?>
</tr>
<?php
		
	}
?>	
<?php $sbplugin->table_block_end(); ?>
<fieldset>
<?php $form->field($newentity, 'redirect_here')->checkbox([]);  ?><label>#{Redirect to the editor page}</label>
<?php $form->field($newentity, 'build')->checkbox([]);  ?><label>#{Compile this entity}</label><br />
<label>#{View}</label><?php $form->field($newentity, 'view')->text([]); ?> 
<label>#{Auth controller}</label><?php $form->field($newentity, 'auth_con')->ComboBox($elist,['htmlattrs'=>['class'=>'fldtype',]]);  ?>
</fieldset>
<?php 
if($mode=='create')
	$form->submit('#{MAKE ENTITY}'); 
else
	$form->submit('#{SAVE ENTITY}');
?>
<?php $form->close(); 
jq_onready($this, "$('#fields_block').jqStructBlock({
	onadd:function(newblock)
		{
			on_type_change($(newblock).find('select.fldtype'))
		}
});");
?>