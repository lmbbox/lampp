<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		if ('dashboard' != $this->uri->segment(1))
		{
			redirect('/dashboard');
		}
	}
	
	public function index()
	{
		$this->load->view('dashboard');
	}
	
	public function phpinfo()
	{
		ob_start();
		phpinfo();
		$phpinfo = ob_get_contents();
		ob_end_clean();
		
		$data = array();
		// the name attribute "module_Zend Optimizer" of an anker-tag is not xhtml valide, so replace it with "module_Zend_Optimizer"
		$data['phpinfo'] = str_replace('module_Zend Optimizer', 'module_Zend_Optimizer', preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpinfo));
		
		$this->load->view('dashboard/phpinfo', $data);
	}
}

/* End of file dashboard.php */
/* Location: ./application/controllers/dashboard.php */