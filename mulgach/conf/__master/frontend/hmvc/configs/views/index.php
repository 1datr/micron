<table>
<?php
//print_r($files);
foreach ($files as $the_file)
{
	if(!is_dir($the_file))
	{
		continue;
	}
	?>
	<tr>
	<td><a href="<?=as_url('hmvc/'.basename($the_file))?>"><?=basename($the_file)?></a></td>
	<td>
	<a href="<?=as_url('configs/editconf/'.basename($the_file))?>" title="#{Change database config}">	
		<img alt="" src="<?=$this->get_image('../../images/icon_conf.png')?>" width="18px" height="18px">		
	</a>
	<a href="<?=as_url('hmvc/connectdb/'.basename($the_file))?>" title="#{Connect config the database}">
		<img alt="" src="<?=$this->get_image('../../images/dbconnect.jpg')?>" width="18px" height="18px">
	<a />
	</td>	
	<td>
		<a href="<?=as_url('lang/'.basename($the_file))?>" title="#{Translation}">
		<img src="<?=$this->get_image('../../images/lang.png')?>" width="18px" height="18px" alt="#{Translation}" />
		</a>
	</td>
	
	<td>
	<?php
	if(basename($the_file)!=$curr_config)
	{
		?>
		
		<a href="<?=as_url('configs/drop/'.basename($the_file))?>" class="ref_delete" conf_message="#{Delete this configuration?}" title="Drop it">
			<img alt="" src="<?=$this->get_image('../../images/trash-circle.png')?>" width="18px" height="18px" />
		</a>
		<?php 
	}
	?>
		</td>
		
	<td>
	<?php
	if(basename($the_file)!=$curr_config)
	{
		?>
			<?php 
			$frm = new mulForm(as_url('configs/setcurrent'),$this,['htmlattrs'=>['style'=>'margin:0px;padding:0px;']]);
			?>
			<button style = "width: 20px; height:20px; padding:0px;margin:0px;cursor:hand;border:none;" title="#{Set current}">
			<img src="<?=$this->get_image('../../images/makecurr.jpg')?>" width="18px" height="18px"  />
			<input type="hidden" name="cfg" value="<?=basename($the_file)?>" />
			</button>
			<?php 
			$frm->close();
			?>
		
		
		
		<?php 
	}
	?>
	</td>
	
	<td>
		<a href="<?=as_url('configs/pack/'.basename($the_file))?>" title="#{Make configuration archive}" >
			<img alt="" src="<?=$this->get_image('../../images/pack.png')?>" width="18px" height="18px" />
		</a>
	</td>
	
	</tr>
	<?php 
}
?>
</table>
<?php 
$frm=new mulForm(as_url("configs/new"),$this);
?>
<label for="newcfg">#{New config name}</label>
<input type="text" id="newcfg" name="newcfg">
<input type="submit" class="btn btn-primary" value="#{Create new config}" />
<?php 
$frm->close();
?>

<?php 
$this->out_view('install_cfg',array());
?>