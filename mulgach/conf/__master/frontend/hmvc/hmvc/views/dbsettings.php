<?php
$form = new mulForm(as_url("hmvc/setdbcfg"),$this);
$form->custom_error_div('connect');
// 'driver'
?>
<div id="drv_params">
<?php 
$model_row->draw_def_form($form);
?>
</div>

<input type="hidden" name="back_url" value="<?=$_SERVER['HTTP_REFERER']; ?>" />
<?php $form->submit('#{NEXT}'); ?>
</form>