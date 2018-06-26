<?php 
$form=new mulForm(as_url("configs/install"),$this,[]);


// обавляем поле к стандартной модели

//;
$this->UseModel($newcfg_model);
$nc_row = $this->_MODEL->empty_row_form_model();
$nc_row->setField('pid',$sp->PID);
$nc_row->setField('passw',$sp->PASSW);
$nc_row->setField('newcfgname',$sp->Data('name_must_be'));
?>


<?php $form->field($nc_row, 'pid')->hidden() ?>
<?php $form->field($nc_row, 'passw')->hidden() ?>
<?php $form->field($nc_row, 'newcfgname')->text() ?>

<?php $form->submit('#{NEXT>}'); ?>
<?php $form->close(); ?>

