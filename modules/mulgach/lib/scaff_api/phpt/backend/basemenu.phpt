<#php
$_DATA=array(
<?php
foreach($tables as $tblidx => $tblinfo)
{
	?>
	array('url'=>'<?=$tblinfo?>','capt'=>'#{<?=$tblinfo?>}'),
	<?php
}
?>
);