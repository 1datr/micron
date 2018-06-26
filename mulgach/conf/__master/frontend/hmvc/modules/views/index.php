<?php 
use BootstrapCombobox\ComboboxWidget as ComboboxWidget;
?>
<?php 
$frm = new mulForm(as_url('modules/makeplg'),$this);
?>
<h4><?=Lang::__t('Create plugin') ?></h4>
<table>
<tr>
	<td><label><?=Lang::__t('Select module:')?></label></td>
	<td><?php $this->usewidget(new ComboboxWidget(),array('data'=>$modules,'name'=>'module')); ?></td>
</tr>
<tr>
	<td><label><?=Lang::__t('New plugin name:')?></label></td>
	<td><input type="text" name="plgname" value="" /></td>
</tr>
<tr><td></td>
	<td><?php 
	$frm->submit(Lang::__t('Create plugin'));
	?>
	</td>
</tr>
</table>




<?php 
$frm->close();
?>
