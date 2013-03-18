<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Manage Crontab - LAMPP</title>
	
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
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/dataTables.TableTools-2.1.4/js/TableTools.min.js'); ?>"></script>
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/dataTables.editor-1.2.3/js/dataTables.editor.min.js'); ?>"></script>
	
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/bootstrap-2.3.0/js/bootstrap.min.js'); ?>"></script>
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/dataTables.editor.bootstrap-1.2.3/dataTables.bootstrap.js'); ?>"></script>
	<script class="include" type="text/javascript" charset="utf-8" src="<?php echo base_url('libraries/dataTables.editor.bootstrap-1.2.3/dataTables.editor.bootstrap.js'); ?>"></script>
	
	<script type="text/javascript" charset="utf-8" id="init-code">
		
		var editor; // use a global for the editor
		
		$(document).ready(function()
		{
			editor = new $.fn.dataTable.Editor(
			{
				'ajaxUrl': '<?php echo site_url('crontab/crud'); ?>',
				'domTable': '#crontab',
				'idSrc': 'job',
				'fields':
				[
					{
						'label': 'Enabled:',
						'name': 'enabled.value',
						'type': 'select',
						'default': 0,
					},
					{
						'label': 'Minute:',
						'name': 'minute',
						'type': 'text',
						'default': '0',
					},
					{
						'label': 'Hour:',
						'name': 'hour',
						'type': 'text',
						'default': '*',
					},
					{
						'label': 'Day of Month:',
						'name': 'dom',
						'type': 'text',
						'default': '*',
					},
					{
						'label': 'Month:',
						'name': 'month',
						'type': 'text',
						'default': '*',
					},
					{
						'label': 'Day of Week:',
						'name': 'dow',
						'type': 'text',
						'default': '*',
					},
					{
						'label': 'Special:',
						'name': 'special',
						'type': 'select',
						'default': '',
						'ipOpts':
						[
							{ 'label': '', 'value': '', },
							{ 'label': '@reboot', 'value': '@reboot', },
							{ 'label': '@yearly', 'value': '@yearly', },
							{ 'label': '@annually', 'value': '@annually', },
							{ 'label': '@monthly', 'value': '@monthly', },
							{ 'label': '@weekly', 'value': '@weekly', },
							{ 'label': '@daily', 'value': '@daily', },
							{ 'label': '@midnight', 'value': '@midnight', },
							{ 'label': '@hourly', 'value': '@hourly', },
						],
					},
					{
						'label': 'Command:',
						'name': 'command',
						'type': 'text',
						'default': '',
					},
				],
			} );
			
			$('#crontab').dataTable(
			{
				'sDom': "<'row-fluid'<'span6'T><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
				'sAjaxSource': '<?php echo site_url('crontab/get'); ?>',
				'bPaginate': false,
				'aoColumns':
				[
					{ 'mData': 'enabled.label', 'sDefaultContent': '', },
					{ 'mData': 'minute', },
					{ 'mData': 'hour', },
					{ 'mData': 'dom', },
					{ 'mData': 'month', },
					{ 'mData': 'dow', },
					{ 'mData': 'special', },
					{ 'mData': 'command', },
					{ 'mData': 'job', 'sDefaultContent': '', 'bVisible': false, },
				],
				'oTableTools':
				{
					'sRowSelect': 'single',
					'aButtons':
					[
						{ 'sExtends': 'editor_create', 'editor': editor, 'sButtonText': '<i class="icon-plus icon-large"></i> New', },
						{ 'sExtends': 'editor_edit',   'editor': editor, 'sButtonText': '<i class="icon-pencil icon-large"></i> Edit', },
						{ 'sExtends': 'editor_remove', 'editor': editor, 'sButtonText': '<i class="icon-remove icon-large"></i> Delete', },
						{ 'sExtends': 'select_all', 'sButtonText': '<i class="icon-check icon-large"></i> Select all', },
						{ 'sExtends': 'select_none', 'sButtonText': '<i class="icon-check-empty icon-large"></i> Deselect all', },
					],
				},
				'fnInitComplete': function ( settings, json )
				{
					// Set the allowed values for the select and radio fields based on
					// what is available in the database
					editor.field('enabled.value').update( json.data.fields.enabled );
				},
			} );
			
		} );
		
	</script>
</head>
<body>
<?php $this->load->view('menu'); ?>
	<div class="container-fluid">
		
		<div class="page-header">
			<h1>Manage Crontab</h1>
		</div>
		<table id="crontab" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" width="100%">
			<thead>
				<tr>
					<th width="10%">Enabled</th>
					<th width="10%">Minute</th>
					<th width="10%">Hour</th>
					<th width="10%">Day of Month</th>
					<th width="10%">Month</th>
					<th width="10%">Day of Week</th>
					<th width="10%">Special</th>
					<th width="30%">Command</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>Enabled</th>
					<th>Minute</th>
					<th>Hour</th>
					<th>Day of Month</th>
					<th>Month</th>
					<th>Day of Week</th>
					<th>Special</th>
					<th>Command</th>
				</tr>
			</tfoot>
		</table>
		
	</div>
</body>
</html>