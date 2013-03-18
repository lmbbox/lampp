	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
				<ul class="nav">
					<li class="<?php echo ('dashboard' == uri_string()) ? 'active' : ''; ?>"><a href="<?php echo site_url('dashboard'); ?>"><i class="icon-dashboard icon-large"></i> Dashboard</a></li>
					<li class="<?php echo ('dashboard/phpinfo' == uri_string()) ? 'active' : ''; ?>"><a href="<?php echo site_url('dashboard/phpinfo'); ?>"><i class="icon-info-sign icon-large"></i> phpinfo()</a></li>
					<li class="<?php echo ('sites' == uri_string()) ? 'active' : ''; ?>"><a href="<?php echo site_url('sites'); ?>"><i class="icon-sitemap icon-large"></i> Sites</a></li>
					<li class="<?php echo ('crontab' == uri_string()) ? 'active' : ''; ?>"><a href="<?php echo site_url('crontab'); ?>"><i class="icon-time icon-large"></i> Crontab</a></li>
					<li class="<?php echo ('phpmyadmin' == uri_string()) ? 'active' : ''; ?>"><a href="/phpmyadmin" target="_blank"><i class="icon-user icon-large"></i> phpMyAdmin</a></li>
				</ul>
			</div>
		</div>
	</div>