<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crontab extends CI_Controller {
	
	protected $fields = array(
		'enabled' => array(
			0 => array(
				'label' => 'No',
				'value' => 0,
			),
			1 => array(
				'label' => 'Yes',
				'value' => 1,
			),
		),
	);
	
	public function __construct()
	{
		parent::__construct();
		
		// load libraries
		$this->load->library('Crontab_Manager');
	}
	
	public function index()
	{
		$this->load->view('crontab');
	}
	
	public function check()
	{
		try
		{
			$cronjobs = Crontab_Manager::getJobs();
			var_dump($cronjobs);
		}
		catch (Exception $e)
		{
			var_dump('ERROR: ' . $e->getMessage(), $e->getCode());
		}
		
		foreach ($cronjobs as $job)
		{
			try
			{
				$job = Crontab_Manager::parseJob($job);
				var_dump($job);
				var_dump(Crontab_Manager::validJobFields($job, $errors), $errors);
			}
			catch (Exception $e)
			{
				var_dump($e->getMessage());
			}
		}
	}
	
	public function get()
	{
		// ajax requests only
		if (!$this->input->is_ajax_request())
		{
			redirect('/crontab');
		}
		
		// load data
		try
		{
			$cronjobs = Crontab_Manager::getJobs();
		}
		catch (Exception $e)
		{
			$cronjobs = array();
		}
		
		$crontab = array();
		foreach ($cronjobs as $job)
		{
			try
			{
				$crontab[] = Crontab_Manager::parseJob($job);
			}
			catch (Exception $e)
			{
				$crontab[] = $e->getMessage();
			}
		}
    	
		// generate json object
		$json = new stdClass();
		$json->id = -1;
		$json->error = '';
		$json->fieldErrors = array();
		$json->data = array(
			'fields' => $this->fields,
		);
		$json->aaData = array();
		
		foreach ($crontab as $id => $job)
		{
			if (!is_array($job))
			{
				$job = array(
					'enabled'	=> FALSE,
					'minute'	=> '',
					'hour'		=> '',
					'dom'		=> '',
					'month'		=> '',
					'dow'		=> '',
					'special'	=> '',
					'command'	=> $job,
					'job'		=> $job,
				);
			}
			
			$json->aaData[] = array(
					'enabled'	=> array(
						'value' => $job['enabled'],
						'label' => $this->fields['enabled'][$job['enabled']]['label'],
					),
					'minute'	=> $job['minute'],
					'hour'		=> $job['hour'],
					'dom'		=> $job['dom'],
					'month'		=> $job['month'],
					'dow'		=> $job['dow'],
					'special'	=> $job['special'],
					'command'	=> $job['command'],
					'job'		=> $job['job'],
			);
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
			redirect('/crontab');
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
				// Get job data
				$job = $this->input->post('data');
				$job['enabled'] = $job['enabled']['value'];
				
				try
				{
					// Check if job is valid
					if (!Crontab_Manager::validJobFields($job, $errors))
					{
						foreach ($errors as $field)
						{
							$json->fieldErrors[] = array(
								'name'		=> $field,
								'status'	=> 'Field is either empty or has an invalid value.',
							);
						}
						throw new Exception('There were errors validating the cronjob.');
					}
					
					Crontab_Manager::addJob($job);
					$json->id				= $job;
					$json->row				= Crontab_Manager::parseJob($job);
					$json->row['job']		= $job;
					$json->row['enabled']	= array(
						'value' => $json->row['enabled'],
						'label' => $this->fields['enabled'][$json->row['enabled']]['label'],
					);
				}
				catch (Exception $e)
				{
					$json->error = $e->getMessage();
					$json->data['error_code'] = $e->getCode();
				}
				break;
			case 'edit':
				// Get job data
				$job = $this->input->post('data');
				$job['enabled'] = $job['enabled']['value'];
				
				try
				{
					// Check if job is valid
					if (!Crontab_Manager::validJobFields($job, $errors))
					{
						foreach ($errors as $field)
						{
							$json->fieldErrors[] = array(
								'name'		=> $field,
								'status'	=> 'Field is either empty or has an invalid value.',
							);
						}
						throw new Exception('There were errors validating the cronjob.');
					}
					
					Crontab_Manager::updateJob($this->input->post('id'), $job);
					$json->id			= $this->input->post('id');
					$json->row			= Crontab_Manager::parseJob($job);
					$json->row['job']	= $job;
					$json->row['enabled']	= array(
						'value' => $json->row['enabled'],
						'label' => $this->fields['enabled'][$json->row['enabled']]['label'],
					);
				}
				catch (Exception $e)
				{
					$json->error = $e->getMessage();
					$json->data['error_code'] = $e->getCode();
				}
				break;
			case 'remove':
				// Get job data
				$jobs = $this->input->post('data');
				
				$json->error = '';
				foreach ($jobs as $job)
				{
					try
					{
						Crontab_Manager::removeJob($job);
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
	
	// Could be public but not needed.
	protected function _clear()
	{
		// ajax requests only
		if (!$this->input->is_ajax_request())
		{
			redirect('/crontab');
		}
		
		// generate json object
		$json = new stdClass();
		
		try
		{
			if (!$this->input->post('confirm'))
			{
				throw new Exception('Please confirm clearing crontab.');
			}
			
			Crontab_Manager::deleteCrontab();
			$json->success	= TRUE;
			$json->message	= 'Crontab cleared';
		}
		catch (Exception $e)
		{
			$json->success		= FALSE;
			$json->message		= $e->getMessage();
			$json->error_code	= $e->getCode();
		}
		
		$data = array();
		$data['json'] = json_encode($json);
		$this->load->view('json', $data);
	}
}

/* End of file crontab.php */
/* Location: ./application/controllers/crontab.php */