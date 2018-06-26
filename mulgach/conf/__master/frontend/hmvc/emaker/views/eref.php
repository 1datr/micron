<?php 
$form = new mulForm(as_url("/emaker/save"),$this,[],false);
?>
<table class="_eref">
<tr>
<td><?php $form->field($row,'entity_to', ['name_root'=>$prefix,'namemode'=>'multi','nameidx'=>$idx])->ComboBox($elist,['htmlattrs'=>['class'=>'_entity_to','onchange'=>'on_entity_change(this)']]);  ?></td>
<td><?php $form->field($row,'fld_to', ['name_root'=>$prefix,'namemode'=>'multi','nameidx'=>$idx])->ComboBox($_fields,['htmlattrs'=>['class'=>'_fld_to','onchange'=>'on_fld_to_change(this)']]);  ?></td>
</tr>
</table>