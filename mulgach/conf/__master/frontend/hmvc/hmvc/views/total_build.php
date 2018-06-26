<?php 
use BootstrapProgressBar\ProgressBarWidget as ProgressBarWidget;
?>
<h3>#{TOTAL UPDATE}</h3>
<?php
$form=new mulForm(as_url("hmvc/maketotal"),$this,['pbid'=>'pb_compile','process'=>'true']);
$this->_MODEL->scenario('total');
$row_total = $this->_MODEL->empty_row_form_model();
$row_total->setField('conf',$config);
$row_total->setField('ep[frontend]',true);
$row_total->setField('ep[backend]',true);
$row_total->setField('ep[install]',true);
$row_total->setField('ep[rest]',true);
$form->scenario($row_total);
?>
<?=$form->field($row_total,'conf')->hidden()?>
<?php $this->usewidget(new ProgressBarWidget(),array(
					//'value'=>0,
					'class'=>'progress-striped active',
					'id'=>'pb_compile',
	)); ?>
<table>
<tr><td><label>#{Rewrite all files}&nbsp;</label></td><td><?=$form->field($row_total,'rewrite_all')->checkbox()?></td></tr>
<tr><td><label>#{Ignore existing settings}&nbsp;</label></td><td><?=$form->field($row_total,'ignore_existing')->checkbox()?></td></tr>
<tr><td><label>#{Autofind authorize controller}&nbsp;</label></td><td><?=$form->field($row_total,'autofind_auth')->checkbox()?></td></tr>
<tr><td colspan="2">
	<label>Frontend</label><?=$form->field($row_total,'ep[frontend]')->checkbox()?>
	<label>Backend</label><?=$form->field($row_total,'ep[backend]')->checkbox()?>
	<label>Install</label><?=$form->field($row_total,'ep[install]')->checkbox()?>
	<label>Rest</label><?=$form->field($row_total,'ep[rest]')->checkbox()?>
</td></tr>
</table>

<?php $form->submit('#{NEXT >}'); ?>
<?php $form->close(); ?>