<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sites_model extends CI_Model {
	
	protected $CI				= NULL;
	protected $bin_root 		= NULL;
	protected $sites_root		= NULL;
	protected $sites_template	= NULL;
	protected $fields			= NULL;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->CI =& get_instance();
		$this->CI->load->helper('file');
		
		// Load configs
		$this->bin_root			= $this->CI->config->item('app_bin_root');
		$this->sites_root		= $this->CI->config->item('app_sites_root');
		$this->sites_template	= $this->CI->config->item('app_sites_template');
		$this->fields			= $this->CI->config->item('app_sites_fields');
	}
	
	protected function _exec($command, &$output = array())
	{
		if (empty($command))
		{
			throw new Exception('Missing or incorrect parameter(s).');
		}
		
		// Make sure error output is redirected
		if ('2>&1' != substr($command, 4))
		{
			$command = $command . ' 2>&1';
		}
		
		// Set empty variables and execute command
		$output = array();
		$return_var = NULL;
		exec($command, $output, $return_var);
		
		// Check if command was successful
		if (0 !== $return_var)
		{
			throw new Exception(implode("\n", $output), $return_var);
		}
		
		return TRUE;
	}
	
	protected function _build_fullname(&$site)
	{
		$this->validate_site($site);
		$site['fullname'] = $site['name'] . $site['suffix'];
		return $site['fullname'];
	}
	
	protected function _parse_fullname($fullname)
	{
		if (empty($fullname) || !is_string($fullname))
		{
			throw new Exception('Missing or incorrect parameter(s).');
		}
		
		$suffixes = array_keys($this->fields['suffix']);
		
		// Sort suffixes by key length
		usort($suffixes, function($a, $b)
		{
			return strlen($b) - strlen($a);
		} );
		
		$regex = '/(?:(.*)(' . implode(')|(.*)(', $suffixes) . '))/i';
		$matches = array();
		if (!preg_match($regex, $fullname, $matches))
		{
			return FALSE;
		}
		
		$parts = array_values(array_filter($matches));
		return array(
			'fullname'	=> $fullname,
			'name'		=> $parts[1],
			'suffix'	=> $parts[2],
		);
	}
	
	public function validate_site($site, &$errors = array())
	{
		$errors				= array();
		$required_fields	= array('name', 'suffix');
		$valid_fields		= array('fullname', 'name', 'suffix', 'template');
		
		// Check required fields
		if (empty($site) || !is_array($site) || 2 !== count(array_intersect($required_fields, array_keys($site))))
		{
			throw new Exception('Missing or incorrect parameter(s).');
		}
		
		// Check each field's value
		foreach ($site as $field => $value)
		{
			if (!in_array($field, $valid_fields))
			{
				unset($site[$field]);
				continue;
			}
			
			// Check field
			switch ($field)
			{
				case 'template':
					if (!isset($this->fields[$field][$value]))
					{
						$errors[] = array(
							'name'		=> $field,
							'status'	=> 'Field has an invalid value.',
						);
					}
					break;
				case 'suffix':
					if (!isset($this->fields[$field][$value]))
					{
						$errors[] = array(
							'name'		=> $field,
							'status'	=> 'Field has an invalid value.',
						);
					}
					// Pass on through to empty and regex checks
				case 'name':
					if (empty($value))
					{
						$errors[] = array(
							'name'		=> $field,
							'status'	=> 'Field is empty.',
						);
					}
					// Pass on through to regex check
				case 'fullname':
				default:
					if (preg_match('/(\.\.)|([^a-z0-9\-\.])/', $value))
					{
						$errors[] = array(
							'name'		=> $field,
							'status'	=> 'Field has an invalid value.',
						);
					}
					break;
			}
		}
		
		return (!empty($errors)) ? FALSE : TRUE;
	}
	
	public function get($fullname = NULL)
	{
		// Normalize parameters
		$fullname = (is_array($fullname) && isset($fullname['fullname'])) ? $fullname['fullname'] : $fullname;
		
		// Get sites from root
		$sites = get_dir_file_info($this->sites_root);
		unset($sites[$this->sites_template]);
		
		foreach ($sites as $key => $site)
		{
			if (!$parts = $this->_parse_fullname($site['name']))
			{
				// Remove sites with unknown suffixes
				unset($sites[$key]);
				continue;
			}
			
			$sites[$key] = array_merge($sites[$key], $parts);
		}
		
		return (!is_null($fullname) ? (!isset($sites[$fullname]) ? FALSE : $sites[$fullname]) : $sites);
	}
	
	public function create(&$site)
	{
		// Normalize parameters
		$site = (is_string($site)) ? $this->_parse_fullname($site) : $site;
		
		$this->_build_fullname($site);
		if (!$this->validate_site($site))
		{
			throw new Exception('Missing or incorrect parameter(s).');
		}
		
		if ($this->get($site))
		{
			throw new Exception("The site already exists.");
		}
		
		$this->_exec("sudo -n '{$this->bin_root}/copy.sh' '{$this->sites_template}' '{$site['fullname']}'");
		
		if (isset($site['template']) && !empty($site['template']))
		{
			try
			{
				$template = $this->fields['template'][$site['template']];
				$this->_exec("sudo -n '{$this->bin_root}/{$template['script']}' '{$site['fullname']}' '{$template['source']}'");
			}
			catch (Exception $e)
			{
				// Delete the site as template failed to deploy
				$this->delete($site);
				throw $e;
			}
		}
		
		return TRUE;
	}
	
	public function move($old, &$new)
	{
		// Normalize parameters
		$old = (is_string($old)) ? $this->_parse_fullname($old) : $old;
		$new = (is_string($new)) ? $this->_parse_fullname($new) : $new;
		
		$this->validate_site($old);
		$this->_build_fullname($new);
		$this->validate_site($new);
		
		if (!$this->get($old))
		{
			throw new Exception("The old site does not exists.");
		}
		
		if ($this->get($new))
		{
			throw new Exception("The new site already exists.");
		}
		
		return $this->_exec("sudo -n '{$this->bin_root}/move.sh' '{$old['fullname']}' '{$new['fullname']}'");
	}
	
	public function delete($site)
	{
		// Normalize parameters
		$site = (is_string($site)) ? $this->_parse_fullname($site) : $site;
		
		$this->validate_site($site);
		
		if (!$this->get($site))
		{
			throw new Exception("The site does not exists.");
		}
		
		return $this->_exec("sudo -n '{$this->bin_root}/remove.sh' '{$site['fullname']}'");
	}
}

/* End of file sites_model.php */
/* Location: ./application/models/sites_model.php */