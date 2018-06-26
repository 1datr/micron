<nav class="navbar navbar-fixed-top  navbar-toggleable-xl navbar-inverse bg-primary ">
<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#nav-content" aria-controls="nav-content" aria-expanded="false" aria-label="Toggle navigation">
<span class="navbar-toggler-icon"></span>
</button>

<!-- Brand -->
<a class="navbar-brand" href="#">MASTER</a>

<!-- Links -->
<div class="collapse navbar-collapse" id="nav-content">   
<ul class="navbar-nav" style="margin-right: auto!important">
<?php       
        foreach ($menu as $punct)
		{
			?>
			<li class="nav-item">
			<a class="nav-link" href="<?=as_url($punct['url']) ?>"><?=$punct['capt'] ?></a>
			</li>
			<?php 
		}
?>
</ul>
<ul class="navbar-nav">
			<li class="nav-item">
			<a class="nav-link" href="<?=as_url('site/logout')?>">#{Logout}&nbsp;(<?=$this->get_user_info('login')?>)</a>
			</li>
</ul>
</nav>




  