<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$config['app_bin_root']			= APPPATH . '/bin';
$config['app_sites_root']		= '/var/www';
$config['app_sites_template']	= 'example.com';
$config['app_sites_fields']		= array(
	'suffix' => array(
		".{$_SERVER['SERVER_ADDR']}.xip.io" => array(
			'label'		=> ".{$_SERVER['SERVER_ADDR']}.xip.io",
			'value'		=> ".{$_SERVER['SERVER_ADDR']}.xip.io",
		),
		'.xip.io' => array(
			'label'		=> '.xip.io',
			'value'		=> '.xip.io',
		),
		'.vcap.me' => array(
			'label'		=> '.vcap.me',
			'value'		=> '.vcap.me',
		),
		'.dev' => array(
			'label'		=> '.dev',
			'value'		=> '.dev',
		),
		'.local' => array(
			'label'		=> '.local',
			'value'		=> '.local',
		),
	),
	'template' => array(
		'' => array(
			'label'		=> 'None',
			'value'		=> '',
			'script'	=> '',
			'source'	=> '',
		),
		'generate_dbinfo' => array(
			'label'		=> 'Generate DB Info',
			'value'		=> 'generate_dbinfo',
			'script'	=> 'generate-dbinfo.sh',
			'source'	=> '',
		),
		'drupal7' => array(
			'label'		=> 'Drupal 7',
			'value'		=> 'drupal7',
			'script'	=> 'drush-restore.sh',
			'source'	=> 'http://example.com/drupal7.tar.gz',
		),
	),
);


/* End of file app.php */
/* Location: ./application/config/app.php */