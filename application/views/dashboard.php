<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Dashboard - LAMPP</title>
	
	<!-- Bootstrap styles -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('libraries/bootstrap-2.3.0/css/bootstrap.min.css'); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('libraries/bootstrap-2.3.0/css/bootstrap-responsive.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('libraries/dataTables.editor.bootstrap-1.2.3/dataTables.bootstrap.css'); ?>" />
	
	<!-- Font Awesome styles -->
	<link type="text/css" href="<?php echo base_url('libraries/font-awesome-3.0.2/css/font-awesome.min.css'); ?>" rel="stylesheet" />
	<!--[if IE 7]>
	<link rel="stylesheet" href="<?php echo base_url('libraries/font-awesome-3.0.2/css/font-awesome-ie7.min.css'); ?>">
	<![endif]-->
	
	<!-- Load custom styles -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/style.css'); ?>" />
	
	<!-- Load the scripts -->
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('js/jquery-1.9.1.min.js'); ?>"></script>
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/dataTables-1.9.4/js/jquery.dataTables.min.js'); ?>"></script>
	
<!-- Not needed here.
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/dataTables.TableTools-2.1.4/js/TableTools.min.js'); ?>"></script>
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/dataTables.editor-1.2.3/js/dataTables.editor.min.js'); ?>"></script>
-->
	
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/bootstrap-2.3.0/js/bootstrap.min.js'); ?>"></script>
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/dataTables.editor.bootstrap-1.2.3/dataTables.bootstrap.js'); ?>"></script>
<!-- Not needed here.
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/dataTables.editor.bootstrap-1.2.3/dataTables.editor.bootstrap.js'); ?>"></script>
-->
	
	<script type="text/javascript" charset="utf-8" id="init-code">
		
		$(document).ready(function()
		{
			$('#sites').dataTable(
			{
				'sDom': "<'row-fluid'<'span6'T><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
				'sAjaxSource': '<?php echo site_url('sites/get'); ?>',
				'bPaginate': false,
				'aoColumns':
				[
					{
						'mData': 'fullname',
						'mRender': function ( data, type, full )
						{
							return '<a href="http://' + data + '" target="_blank">' + data + '</a>';
						},
					},
					{ 'mData': 'server_path', },
				],
			} );
		} );
		
	</script>
</head>
<body>
<?php $this->load->view('menu'); ?>
	<div class="container-fluid">
		
		<div class="page-header">
			<h1>LAMPP Dashboard</h1>
		</div>
		
		<h2>Server Details</h2>
		<div class="well well-large">
			<dl class="dl-horizontal">
				<dt>Hostname</dt>
				<dd><?php echo gethostname(); ?></dd>
				<dt>IP</dt>
				<dd><?php echo $_SERVER['SERVER_ADDR']; ?></dd>
				<dt>Username</dt>
				<dd>admin</dd>
				<dt>Password</dt>
				<dd>pass</dd>
				<br />
				<dt>Web Server</dt>
				<dd><?php echo $_SERVER['SERVER_SOFTWARE']; ?></dd>
				<br />
				<dt>PHP Details</dt>
				<dd><a href="<?php echo site_url('dashboard/phpinfo'); ?>" class="btn btn-primary"><i class="icon-info-sign icon-large"></i> phpinfo()</a></dd>
				<dt>Version</dt>
				<dd><?php echo phpversion(); ?></dd>
				<dt>Memory Limit</dt>
				<dd><?php echo ini_get('memory_limit'); ?></dd>
				<dt>Upload Max Filesize</dt>
				<dd><?php echo ini_get('upload_max_filesize'); ?></dd>
				<br />
				<dt>MySQL Details</dt>
				<dd>&nbsp;</dd>
				<dt>Host</dt>
				<dd>localhost</dd>
				<dt>Username</dt>
				<dd>root</dd>
				<dt>Password</dt>
				<dd>pass</dd>
				<br />
				<dt>Memcached Details</dt>
				<dd>&nbsp;</dd>
				<dt>Host</dt>
				<dd>localhost</dd>
				<dt>Port</dt>
				<dd>11211</dd>
			</dl>
		</div>
		<hr>
		<h2>Sites</h2>
		<table id="sites" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" width="100%">
			<thead>
				<tr>
					<th width="30%">Name</th>
					<th width="70%">Server Path</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>Name</th>
					<th>Server Path</th>
				</tr>
			</tfoot>
		</table>
		
	</div>
</body>
</html>