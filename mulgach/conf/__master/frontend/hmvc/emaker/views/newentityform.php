<?php 
			$frm = new mulForm(as_url('emaker/creationform'),$this,['htmlattrs'=>['style'=>'margin:0px;']]);

			$frm->field($newrow, 'cfg')->hidden([]);  
			?>
			<h4>#{CREATE NEW ENTITY}</h4>
			<table>
			<tr>
			<td><?php $frm->field($newrow, 'ename')->text(['htmlattrs'=>['placeholder'=>Lang::__t('Entity name'),]]); ?></td>
			<td><?php $frm->field($newrow, 'auth_entity')->checkbox() ?><label>#{FOR AUTH}</label></td>
			<td>
			
			</td>
			</tr>
			</table>
			<?php
			$frm->submit("MAKE");
			
			$frm->close();
?>
