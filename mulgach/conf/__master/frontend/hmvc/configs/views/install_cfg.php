<?php 
$frm=new mulForm(as_url("configs/install"),$this,['pbid'=>'pb_installcfg','process'=>'true']);
?>
<label for="newcfg">#{New config name}</label>
<input type="file" name="cfgfile" accept="application/zip">
<input type="submit" class="btn btn-primary" value="#{Install config}"  />
<?php 
$frm->close();
?>
