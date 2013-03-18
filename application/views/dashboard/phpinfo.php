<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Environment Configuration - LAMPP</title>
	
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
<!-- Not needed here.
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/dataTables-1.9.4/js/jquery.dataTables.min.js'); ?>"></script>
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/dataTables.TableTools-2.1.4/js/TableTools.min.js'); ?>"></script>
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/dataTables.editor-1.2.3/js/dataTables.editor.min.js'); ?>"></script>
-->
	
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/bootstrap-2.3.0/js/bootstrap.min.js'); ?>"></script>
<!-- Not needed here.
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/dataTables.editor.bootstrap-1.2.3/dataTables.bootstrap.js'); ?>"></script>
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/dataTables.editor.bootstrap-1.2.3/dataTables.editor.bootstrap.js'); ?>"></script>
-->
	
	<script type="text/javascript" charset="utf-8" id="init-code">
		
	</script>
	
	<style type="text/css">
		#phpinfo {}
		#phpinfo pre {margin: 0px; font-family: monospace;}
		#phpinfo a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
		#phpinfo a:hover {text-decoration: underline;}
		#phpinfo table {border-collapse: collapse;}
		#phpinfo .center {text-align: center;}
		#phpinfo .center table {margin-left: auto; margin-right: auto; text-align: left;}
		#phpinfo .center th {text-align: center !important;}
		#phpinfo td, th {border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
		#phpinfo h1 {font-size: 150%;}
		#phpinfo h2 {font-size: 125%;}
		#phpinfo .p {text-align: left;}
		#phpinfo .e {background-color: #ccccff; font-weight: bold; color: #000000;}
		#phpinfo .h {background-color: #9999cc; font-weight: bold; color: #000000;}
		#phpinfo .v {background-color: #cccccc; color: #000000;}
		#phpinfo .vr {background-color: #cccccc; text-align: right; color: #000000;}
		#phpinfo img {float: right; border: 0px;}
		#phpinfo hr {}
	</style>
</head>
<body>
<?php $this->load->view('menu'); ?>
	<div class="container-fluid">
		
		<div class="page-header">
			<h1>Environment Configuration</h1>
		</div>
		
		<div id="phpinfo">
			<?php echo $phpinfo; ?>
		</div>
		
	</div>
</body>
</html>