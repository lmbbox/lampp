<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sites extends CI_Controller {
	
	protected $fields = array();
	
	public function __construct()
	{
		parent::__construct();
		
		$this->fields = $this->config->item('app_sites_fields');
		$this->load->model('sites_model');
	}
	
	protected function _format_site($site)
	{
		if (is_string($site['suffix']))
		{
			$site['suffix']	= array(
				'value'	=> $site['suffix'],
				'label'	=> $this->fields['suffix'][$site['suffix']]['label'],
			);
		}
		
		return $site;
	}
	
	protected function _format_site_errors($errors)
	{
		foreach($errors as $key => $error)
		{
			if ('suffix' == $error['name'])
			{
				$errors[$key]['name'] = 'suffix.value';
			}
			if ('template' == $error['name'])
			{
				$errors[$key]['name'] = 'template.value';
			}
		}
		
		return $errors;
	}
	
	public function index()
	{
		$this->load->view('sites');
	}
	
	public function get()
	{
		// ajax requests only
		if (!$this->input->is_ajax_request())
		{
			redirect('/sites');
		}
		
		// load data
		$sites = $this->sites_model->get();
		
		// generate json object
		$json = new stdClass();
		$json->id = -1;
		$json->error = '';
		$json->fieldErrors = array();
		$json->data = array(
			'fields' => $this->fields,
		);
		$json->aaData = array();
		
		foreach ($sites as $key => $site)
		{
			$json->aaData[] = $this->_format_site($site);
		}
		
		// Convert field defs to indexed arrays for json / datatable editor support
		foreach ($json->data['fields'] as $key => $value)
		{
			$json->data['fields'][$key] = array_values($value);
		}
		
		$data = array();
		$data['json'] = json_encode($json);
		$this->load->view('json', $data);
	}
	
	public function crud()
	{
		// ajax requests only
		if (!$this->input->is_ajax_request())
		{
			redirect('/sites');
		}
		
		// generate json object
		$json = new stdClass();
		$json->id = -1;
		$json->error = '';
		$json->fieldErrors = array();
		$json->data = array();
		$json->row = array();
		
		// Process request
		switch ($this->input->post('action'))
		{
			case 'create':
				// Get post data
				$data = $this->input->post('data');
				$data['suffix'] = $data['suffix']['value'];
				$data['template'] = $data['template']['value'];
				
				try
				{
					// Check if site is valid
					if (!$this->sites_model->validate_site($data, $errors))
					{
						$json->fieldErrors = $this->_format_site_errors($errors);
						throw new Exception('There were errors validating the site.');
					}
					
					// Try to create site
					$this->sites_model->create($data);
					$site		= $this->sites_model->get($data);
					$json->id	= $site['fullname'];
					$json->row	= $this->_format_site($site);
				}
				catch (Exception $e)
				{
					$json->error = $e->getMessage();
					$json->data['error_code'] = $e->getCode();
				}
				break;
			case 'edit':
				// Get post data
				$data = $this->input->post('data');
				$data['suffix'] = $data['suffix']['value'];
				unset($data['template']);
				
				try
				{
					// Check if site is valid
					if (!$this->sites_model->validate_site($data, $errors))
					{
						$json->fieldErrors = $this->_format_site_errors($errors);
						throw new Exception('There were errors validating the site.');
					}
					
					// Try to rename site
					$this->sites_model->move($this->input->post('id'), $data);
					$site		= $this->sites_model->get($data);
					$json->id	= $this->input->post('id');
					$json->row	= $this->_format_site($site);
				}
				catch (Exception $e)
				{
					$json->error = $e->getMessage();
					$json->data['error_code'] = $e->getCode();
				}
				break;
			case 'remove':
				// Get post data
				$data = $this->input->post('data');
				
				$json->error = '';
				foreach ($data as $id)
				{
					try
					{
						// Try to delete site
						$this->sites_model->delete($id);
					}
					catch (Exception $e)
					{
						$json->error .= $e->getMessage() . "\n";
						$json->data['error_code'][] = $e->getCode();
					}
				}
				break;
			default:
				$json->error = 'Invalid Request';
				break;
		}
		
		$data = array();
		$data['json'] = json_encode($json);
		$this->load->view('json', $data);
	}
}

/* End of file sites.php */
/* Location: ./application/controllers/sites.php */