<?php 
use BootstrapCombobox\ComboboxWidget as ComboboxWidget;
?>
<h3>#{CAPTIONS}</h3>
<?php 
$table = $_SESSION['makeinfo']['table'];

$eps = $_SESSION['makeinfo']['ep'];
unset($eps['rest']);
$eps = array_keys($eps);
?>
<ul class="nav nav-tabs" role="tablist">
<?php 
foreach ($eps as $idx => $_ep)
{
	?>
	<li class="nav-item">
	<a class="nav-link <?=(($idx==0)?'active':'')?>" data-toggle="tab" href="#capts_<?=$_ep?>" role="tab"><?=$_ep?></a>
	</li>		
	<?php	
}
?>
</ul>
<div class="tab-content">
<?php 
//print_r($_SESSION);
foreach ($eps as $idx => $_ep)
{
	?>
	<div class="tab-pane <?=(($idx==0)?'active':'')?> tab-page" id="capts_<?=$_ep?>" role="tabpanel">
	<table>
  	<tr>
    	<th>#{FieldName}</th>
    	<th>#{FieldCaption}</th>
  	</tr>
	<?php 
	foreach ($table_info['fields'] as $fld => $fldinfo)
	{
		?>
		<tr>
		<th><?=$fld?></th>
		<?php 
		$thelang=new Lang(NULL, $_SESSION['makeinfo']['conf'],$_ep);
		$lang_val = $thelang->getkey($table.'.'.$fld);
		?>
		<td><input type="text" name="captions[<?=$_ep?>][<?=$table?>.<?=$fld?>]" value="<?=($lang_val!=NULL) ? $lang_val: $fld?>" /></td>
		</tr>
		<?php 
	}
	?>
	</table>
	</div>
	<?php 
}
?>


<?php 
$eps=array('frontend','backend');
?>


<h3>#{MAIN MENU}</h3>

<ul class="nav nav-tabs" role="tablist">
<?php 
	foreach ($eps as $idx => $_ep)
	{
	?>
	<li class="nav-item">
		<a class="nav-link <?=(($idx==0)?'active':'')?>" data-toggle="tab" href="#mainmenu_<?=$_ep?>" role="tab"><?=$_ep?></a>
	</li>		
	<?php
	}
?>		
</ul>
<div class="tab-content">
<?php 

	foreach ($eps as $idx => $_ep)
	{
		$_checked="";
	?>
	<div class="tab-pane <?=(($idx==0)?'active':'')?> tab-page" id="mainmenu_<?=$_ep?>" role="tabpanel">
	<label for="cb_menu_<?=$_ep?>">#{Generate main menu}</label>
	<input id="cb_menu_<?=$_ep?>" type="checkbox" name="mainmenu[<?=$_ep?>]" <?=( (!empty($table_info['mainmenu'][$_ep])) ? 'checked' : '') ?>  onchange="$('#connect_from_<?=$_ep?>').toggle();" />
	
	<?php 
	//print_r($_SESSION);
	?>
	
		<div id="connect_from_<?=$_ep?>" <?=( (!empty($table_info['mainmenu'][$_ep])) ? 'style="display:none"' : '') ?>>
		<label for="menu_connect_from_<?=$_ep?>">#{Connect menu from :}</label>
		<?php $this->usewidget(new ComboboxWidget(),array('data'=>$triads[$_ep],
						'name'=>"connect_from[".$_ep."]",					
						//'value'=>$con['model'],
						'value'=> ( (!empty($table_info['connect_from'][$_ep])) ? $table_info['connect_from'][$_ep] : (isset($_SESSION['connect_from'][$_ep]) ? $_SESSION['connect_from'][$_ep] : '')),
						'htmlattrs'=>array())
						); ?>
		</div>
	</div>
	<?php
	}
?>	
</div>

</div>
