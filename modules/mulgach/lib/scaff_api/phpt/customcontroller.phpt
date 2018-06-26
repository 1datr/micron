<#php 
namespace {_CONFIG}\{_EP};

class <?=UcaseFirst($triada)?>Controller extends \BaseController
{

<?php
foreach($actions as $idx => $action)
{
	if(!empty($action))
	{
	?>
		public function Action<?=UcaseFirst($action['name'])?>()
		{
			<?php 
			if(isset($action['automakeview']))
			{
				?>
				$this->out_view('<?=$action['name']?>',array());
				<?php
			}
			?>
		}
	<?
	}
}
?>
	
	
}
#>