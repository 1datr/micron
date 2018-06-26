
<?php 
$sbplugin->template_table_start('actions_item');
?>
	<td><input type="text" name="actions[{idx}][name]"  class="" /></td>	
	<td><input type="checkbox" name="actions[{idx}][automakeview]" checked class="auto" /></td>		
	<td><button type="button" class="actions_item_drop">x</button></td>

<?php 
$sbplugin->template_table_end();
?>
<?php 
$form=new mulForm(as_url("hmvc/makepure/"),$this);
?>
<h3>#{CREATE FROM ACTIONLIST}</h3>
<input type="hidden" name="conf" value="<?=$config?>" />
<label for="newtriada">#{New triada name}</label>
<input type="text" id="newtriada" name="triada" value="" /><br />
<h4>#{Enter actions of new controller}</h4>

<?php 
$sbplugin->table_block_start('actions_item',array('id'=>'actions_block'),array(),'
		<thead>
		<tr><th width="320">#{Action name}</th><th>#{Autogenerate view}</th></tr>
		</thead>
		');
?>
<tr role="item">
	<td><input type="text" name="actions[0][name]"  class="" value="index" /></td>	
	<td><input type="checkbox" name="actions[0][automakeview]" checked class="auto" /></td>		
	<td><button type="button" class="actions_item_drop">x</button></td>
</tr>
<?php 
$sbplugin->table_block_end(function(){
		?>
		<tfooot>
		<tr>
		<td colspan="2">
			<button type="button" class="actions_item_add" title="#{ADD_ACTION}">+</button>
		</td>
		</tr>
		</tfooot>
		<?php 
	});

jq_onready($this,"
								$( document ).ready(function() {
									$('#actions_block').jqStructBlock();
								});
								");
?>
<label>#{Rewrite all files}&nbsp;</label><input type="checkbox" name="rewrite_all" ><br />
<label>Frontend&nbsp;</label><input type="checkbox" name="ep[frontend]" checked>
<label>Backend&nbsp;</label><input type="checkbox" name="ep[backend]" checked>
<label>Install&nbsp;</label><input type="checkbox" name="ep[install]" checked>
<label>REST&nbsp;</label><input type="checkbox" name="ep[rest]" checked >
<br />
<?php $form->submit('#{CREATE HMVC}'); ?>
</form>
