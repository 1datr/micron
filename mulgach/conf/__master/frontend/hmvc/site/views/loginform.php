<?php
$frm=new mulForm(as_url('site/auth'),$this);
?>
<div class="error" id="err_auth" role="alert"></div>
<table>
<tr><th>#{Login}</th><td><input type="text" name="login" /></td></tr>
<tr><th>#{Password}</th><td><input type="password" name="password" /></td></tr>
<tr></tr>
</table>
<?php 
$dismissed_url_var = $this->get_user_descriptor().'_dismissed_url';
?>
<input type="hidden" name="url_required" value="<?=(isset($_SESSION[$dismissed_url_var]))? $_SESSION[$dismissed_url_var] : ""?>" />
<?php $frm->submit('#{Log in}'); ?>