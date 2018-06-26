<?php 
$frm = new mulForm(as_url('configs/setdbcfg'),$this);
?>
<input type="hidden" name="cfg" value="<?=$cfg?>" />
<table>
<tr>
<td><label>#{Config file code}</label></td>
<td><textarea name="code" cols="60" rows="12"><?=htmlentities($conf_code)?></textarea></td>
</tr>
<tr>
<td></td>
<td>
<?php $frm->submit('#{Set configuration code}'); ?>
</td>
</tr>
</table>
<?php 
$frm->close();
?>
