<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Manage Sites - LAMPP</title>
	
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
				'ajaxUrl': '<?php echo site_url('sites/crud'); ?>',
				'domTable': '#sites',
				'idSrc': 'fullname',
				'i18n':
				{
					'remove':
					{
						'confirm':
						{
							'_': 'The site files will be permanently deleted and cannot be recovered. Are you sure you wish to delete these %d sites?',
							'1': 'The site files will be permanently deleted and cannot be recovered. Are you sure you wish to delete this site?',
						},
					},
				},
				'fields':
				[
					{
						'label': 'Name:',
						'name': 'name',
						'type': 'text',
						'default': '',
					},
					{
						'label': 'Suffix:',
						'name': 'suffix.value',
						'type': 'select',
						'default': '',
					},
					{
						'label': 'PHP Version:',
						'name': 'phpversion.value',
						'type': 'select',
						'default': '',
					},
					{
						'label': 'Aliases:',
						'name': 'aliases',
						'type': 'textarea',
						'default': '',
					},
					{
						'label': 'Template:',
						'name': 'template.value',
						'type': 'select',
						'default': '',
					},
				],
				'events':
				{
					'onInitCreate': function ( )
					{
						this.show('template.value');
					},
					'onInitEdit': function ( )
					{
						this.hide('template.value');
					},
				},
			} );
			
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
				'oTableTools':
				{
					'sRowSelect': 'single',
					'aButtons':
					[
						{ 'sExtends': 'editor_create', 'editor': editor, 'sButtonText': '<i class="icon-plus icon-large"></i> New', },
						{ 'sExtends': 'editor_edit',   'editor': editor, 'sButtonText': '<i class="icon-pencil icon-large"></i> Edit', },
						{ 'sExtends': 'editor_remove', 'editor': editor, 'sButtonText': '<i class="icon-remove icon-large"></i> Delete', },
					//	{ 'sExtends': 'select_all', 'sButtonText': '<i class="icon-check icon-large"></i> Select all', },
					//	{ 'sExtends': 'select_none', 'sButtonText': '<i class="icon-check-empty icon-large"></i> Deselect all', },
					],
				},
				'fnInitComplete': function ( settings, json )
				{
					// Set the allowed values for the select and radio fields based on
					// what is available in the database
					editor.field('suffix.value').update( json.data.fields.suffix );
					editor.field('phpversion.value').update( json.data.fields.phpversion );
					editor.field('template.value').update( json.data.fields.template );
				},
			} );
		} );
		
	</script>
</head>
<body>
<?php $this->load->view('menu'); ?>
	<div class="container-fluid">
		
		<div class="page-header">
			<h1>Manage Sites</h1>
		</div>
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