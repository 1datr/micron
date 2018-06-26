<?php 
$form=new mulForm(as_url("hmvc/maketotal"),$this,[]);


// обавляем поле к стандартной модели

//;
$this->UseModel($sp_model);
$sp_row = $this->_MODEL->empty_row_form_model();
$sp_row->setField('pid',$sp->PID);
$sp_row->setField('passw',$sp->PASSW);
?>
<?php $form->field($sp_row, 'pid')->hidden() ?>
<?php $form->field($sp_row, 'passw')->hidden() ?>
<h4>#{Finded several tables to be authorize}</h4>
<label>#{Choose authorization table}</label>
<?php $form->field($sp_row, 'settings[authcon]')->ComboBox($authcons) ?>
<?php $form->submit('#{SELECT}'); ?>
<?php $form->close(); ?>

