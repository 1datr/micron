<h3>#{Translations} <?=$config?></h3>

<ul class="nav nav-tabs">
<?php 
$eps=array('frontend','backend','install');
foreach ($eps as $idx => $ep)
{
	$flg = (isset($current_ep)) ? ($current_ep==$ep) : ($idx==0);
	?>
	<li class="nav-item"><a data-toggle="tab" role="tab" class="nav-link  <?=(($flg)?'active':'')?>" href="#lang_<?=$ep?>"><?=$ep?></a></li>
	<?php 
}
?>
</ul>
<?php 
$this->inline_css('
.tab-page {
	padding: 10px;
	border-left: 1px solid #ddd;
	border-bottom: 1px solid #ddd;
	border-right: 1px solid #ddd;
	border-bottom-right-radius: 10px;
	border-bottom-left-radius: 10px;
}		
');
?>
<div class="tab-content">
<?php foreach ($eps as $idx => $ep)
{
	$flg = (isset($current_ep)) ? ($current_ep==$ep) : ($idx==0);
	?>
	<div id="lang_<?=$ep?>" class="tab-pane <?=( ($flg) ?'active':'')?> tab-page" role="tabpanel">
 	<?php 
 	$this->out_view('searchform',array('request'=>$request,'ep'=>$ep,'config'=>$config,'dr'=>$dr));
 	if(isset($current_ep))
 	{
	 	if($ep==$current_ep)
	 	{
			if(count($results)>0)
				$this->out_view('results',array('results'=>$results,'ep'=>$ep,'config'=>$config,'lang'=>$lang));
	 	}
 	}
	?>
 	</div>
	<?php 
}
?>
</div>

<?php 

?>