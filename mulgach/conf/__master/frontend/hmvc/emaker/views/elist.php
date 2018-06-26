<table>
<tr><th>#{ENTITY NAME}</th><th>#{COMPILED}</th></tr>
<?php
foreach ($entities_table as $entity)
	{
?>
	<tr>
	<td><?=$entity->NAME?></td>
	<td>
	<?php
	if($entity->COMPILED)
		echo "x";
	?>
	</td>
	<td>
		<a href="<?=as_url('emaker/drop/'.$entity->PARENT_CFG->_NAME.'/'.$entity->NAME)?>" class="ref_delete" conf_message="#{Delete this entity?}" title="Drop it">
			<img alt="" src="<?=$this->get_image('../../images/trash-circle.png')?>" width="18px" height="18px" />
		</a>	
	</td>
	<td>
		<a href="<?=as_url('emaker/edit/'.$entity->PARENT_CFG->_NAME.'/'.$entity->NAME)?>" title="#{Edit the entity}">
			<img alt="" src="<?=$this->get_image('../../images/pen.jpg')?>" width="18px" height="18px">
		<a />
	</td>
	
	</tr>
<?php 	
	}
?>
</table>
<?php 
/*
jq_onready($this,"
$('.ref_delete').click(function(obj) 
        		{
        			if(confirm($(obj).attr('conf_message'))
        			{
        				return true;
        			}
        			return false;
        		});");
        		*/
?>