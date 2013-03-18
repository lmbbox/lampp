<?php

class Crontab_Manager {
	
	// In this class, array instead of string would be the standard input / output format.
	
	// Legacy way to add a job:
	// $output = shell_exec('(crontab -l; echo "'.$job.'") | crontab -');
	
	const CRONTAB_HEADER_COMMENT = '# m	h	dom	mon	dow	command';
	
	static protected function _buildJob($job)
	{
		if (!is_array($job))
		{
			return $job;
		}
		
		return (($job['enabled']) ? '' : '#')
				. ((isset($job['special']) && !empty($job['special'])) ? "{$job['special']}\t" : "{$job['minute']}\t{$job['hour']}\t{$job['dom']}\t{$job['month']}\t{$job['dow']}\t")
				. ((isset($job['user']) && !empty($job['user'])) ? "{$job['user']}\t" : '')
				. "{$job['command']}";
	}
	
	static protected function _cronRegex($usertab = TRUE, $return_field = NULL)
	{
		$numbers = array(
			'minute'	=> '[0-5]?\d',
			'hour'		=> '[01]?\d|2[0-3]',
			'dom'		=> '0?[1-9]|[12]\d|3[01]',
			'month'		=> '[1-9]|1[012]',
			'dow'		=> '[0-6]'
		);
		
		$normal_fields = array();
		foreach ($numbers as $field => $number)
		{
			$range = "(?:$number)(?:-(?:$number)(?:\/\d+)?)?";
			$normal_fields[$field] = "\*(?:\/\d+)?|$range(?:,$range)*";
			
			if ('month' == $field)
			{
				$normal_fields[$field] .= '|jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec';
			}
			elseif ('dow' == $field)
			{
				$normal_fields[$field] .= '|mon|tue|wed|thu|fri|sat|sun';
			}
			
			$normal_fields[$field] = "(?<$field>{$normal_fields[$field]})";
		}
		$normal = implode('\s+', $normal_fields);
		
		$special = '(?<special>@reboot|@yearly|@annually|@monthly|@weekly|@daily|@midnight|@hourly)';
		
		// Return regex for field
		if ('schedule' == $return_field)
		{
			return "/($normal|$special)/i";
		}
		elseif (!is_null($return_field))
		{
			return ('special' == $return_field) ? "/$special/i" : "/{$normal_fields[$return_field]}/i";
		}
		
		return '/^\s*(?:'
				. '$'
				. '|\w+\s*=' // word lines? what the!
				. "|(?<enabled>#?)\s*(?:$normal|$special)\s+"
				. ')'
				. ((!$usertab) ? '(?<user>[^\s]+)\s+' : '')
				. '(?<command>.*)$/i';
	}
	
	static protected function _exec($command, &$output = array())
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
	
	
	
	
	static public function validJob($job, $usertab = TRUE)
	{
		$job = (string) self::_buildJob($job);
		return preg_match(self::_cronRegex($usertab), trim($job));
	}
	
	static public function validJobField($field, $string)
	{
		if (in_array($field, array('user', 'command')))
		{
			return is_string($string) && !empty($string);
		}
		
		return preg_match(self::_cronRegex(TRUE, $field), trim($string));
	}
	
	static public function validJobFields($job, &$errors = array())
	{
		$errors				= array();
		$required_fields	= array('minute', 'hour', 'dom', 'month', 'dow', 'special', 'command');
		$valid_fields		= array('minute', 'hour', 'dom', 'month', 'dow', 'special', 'user', 'command');
		
		// Check required fields
		if (empty($job) || !is_array($job) || 7 !== count(array_intersect($required_fields, array_keys($job))))
		{
			throw new Exception('Missing or incorrect parameter(s).');
		}
		
		// Check special field
		if (!empty($job['special']))
		{
			foreach ($job as $field => $value)
			{
				if (!empty($value) && in_array($field, array('minute', 'hour', 'dom', 'month', 'dow')))
				{
					$errors[] = 'special';
				}
			}
		}
		
		if (empty($errors) && self::validJob($job, !isset($job['user'])))
		{
			return TRUE;
		}
		
		// Check each field's value
		foreach ($job as $field => $value)
		{
			if (!in_array($field, $valid_fields))
			{
				unset($job[$field]);
				continue;
			}
			
			// Check field
			if (!self::validJobField($field, $value))
			{
				$errors[] = $field;
			}
		}
		
		// Check complete schedule section
		unset($job['user']);
		unset($job['command']);
		if (!self::validJobField('schedule', (string) self::_buildJob($job)))
		{
			$errors[] = 'minute';
			$errors[] = 'hour';
			$errors[] = 'dom';
			$errors[] = 'month';
			$errors[] = 'dow';
			$errors[] = 'special';
		}
		elseif (!empty($errors) && in_array('special', $errors))
		{
			// Unset special field error if blank as the schedule section validate
			unset($errors[array_search('special', $errors)]);
		}
		
		return (!empty($errors)) ? FALSE : TRUE;
	}
	
	static public function parseJob($job, $usertab = TRUE)
	{
		if (!preg_match(self::_cronRegex($usertab), trim($job), $match))
		{
			throw new Exception("Invalid cronjob string: $job");
		}
		
		$data = array(
			'job'		=> $match[0],
			'enabled'	=> empty($match['enabled']),
			'minute'	=> $match['minute'],
			'hour'		=> $match['hour'],
			'dom'		=> $match['dom'],
			'month'		=> $match['month'],
			'dow'		=> $match['dow'],
			'special'	=> $match['special'],
			'user'		=> (!$usertab) ? $match['user'] : '',
			'command'	=> $match['command'],
		);
		
		return $data;
	}
	
	
	
	
	static public function deleteCrontab()
	{
		return self::_exec('crontab -r');
	}
	
	static public function getJobs()
	{
		
//		sys_get_temp_dir() . '/' . uniqid('crontab-');
		
		$output = array();
		self::_exec('crontab -l', $output);
		
		// Clean up blank and header comment lines
		foreach ($output as $key => $item)
		{
			if ('' == $item || self::CRONTAB_HEADER_COMMENT == $item)
			{
				unset($output[$key]);
			}
		}
		
		return $output;
	}
	
	static public function saveJobs($jobs, $usertab = TRUE)
	{
		if (empty($jobs) || !is_array($jobs))
		{
			throw new Exception('Missing or incorrect parameter(s).');
		}
		
		foreach ($jobs as $key => $job)
		{
			$job = (string) self::_buildJob($job);
			
			if (!self::validJob($job, $usertab) && '#' != $job[0])
			{
				throw new Exception("Invalid cronjob string: $job");
			}
			
			$jobs[$key] = $job;
		}
		
		// Add header comment line
		$jobs = array_merge(array(self::CRONTAB_HEADER_COMMENT), $jobs);
		
		// Update crontab with jobs
		return self::_exec('echo "' . implode("\n", $jobs) . '" | crontab -');
	}
	
	static public function doesJobExist($job)
	{
		$job = (string) self::_buildJob($job);
		$jobs = self::getJobs();
		return array_search($job, $jobs);
	}
	
	static public function addJob(&$job, $usertab = TRUE)
	{
		if (FALSE !== self::doesJobExist(&$job))
		{
			throw new Exception('Cronjob already exists.');
		}
		
		$jobs = self::getJobs();
		$jobs[] = $job;
		return self::saveJobs($jobs, $usertab);
	}
	
	static public function updateJob($old, &$new, $usertab = TRUE)
	{
		if (FALSE === $key = self::doesJobExist($old))
		{
			throw new Exception('Old cronjob does not exist.');
		}
		
		if (FALSE !== self::doesJobExist(&$new))
		{
			throw new Exception(($old == $new) ? 'There are no changes to apply.' : 'New cronjob already exists.');
		}
		
		$jobs = self::getJobs();
		$jobs[$key] = $new;
		return self::saveJobs($jobs, $usertab);
	}
	
	static public function removeJob($job, $usertab = TRUE)
	{
		if (FALSE === $key = self::doesJobExist($job))
		{
			throw new Exception('Cronjob does not exist.');
		}
		
		$jobs = self::getJobs();
		unset($jobs[$key]);
		return self::saveJobs($jobs, $usertab);
	}
}