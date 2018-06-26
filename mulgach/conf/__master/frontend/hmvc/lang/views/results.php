<table cellspacing="0" cellpadding="0">
  <tr>
    <th>#{LangKey}</th>
    <th>#{LangVal}</th>
  </tr>
<?php
$row = $this->_MODEL->empty_row_form_model();
foreach ($results as $key => $val) {
?>
  <tr>
    <td ><?=$key?></td>
    <td >
    
    <?php
    $row->setField('config',$config);
    $row->setField('ep',$ep);
    $row->setField('lang',$lang);
    $row->setField('langkey',$key);
    $row->setField('translation',$val);
    
    $frm = new mulForm(as_url('lang/set'),$this,['htmlattrs'=>['style'=>"margin:0px;"]]);
    ?>
    <table style="padding:0px;" cellspacing="0" cellpadding="0">
    <tr>
    <td>
    <?php 
    $frm->field($row, 'config')->hidden();
    $frm->field($row, 'ep')->hidden();
    $frm->field($row, 'lang')->hidden();
    $frm->field($row, 'langkey')->hidden();
    $frm->field($row, 'translation')->text();
    ?>
    </td>
    <td>    
    <?php 
    $frm->submit(Lang::__t('save'),'',['class'=>'btn btn-secondary btn-sm']);
    ?>
    </tr>
    </table>
    <?php 
    $frm->close();
    ?>
    
    </td>
  </tr>
<?php 	
}
?>
</table>