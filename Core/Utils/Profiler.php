<?php

/**
 * Hydra Profiler
 *
 * @package Core
 * @subpackage Profiler
 */
class Profiler
{
	private $enabled = false;
	private $startMemory;
	private $endMemory;
	private $peakMemory;
	private $startTime;
	private $endTime;
	
	public $Sql = null;
	public $SqlArgs = null;
	
	function __construct($enabled)
	{
		// Automatically enable/disable the profiler
		$this->enabled = $enabled;
	}
	
	/**
	 * Start the profiler capturing data
	 */
	function start()
	{
		$time = microtime();
		$time = explode(" ", $time);
		$time = $time[1] + $time[0];
		$this->startTime = $time;
		
		$this->startMemory = memory_get_usage();
	}
	
	/**
	 * Stop the profiler and record the collected data
	 */
	function stop()
	{
//		// Override the enablement if the system isn't logged in
//		try
//		{
//			Core::getUser();
//		}
//		catch (HydraCoreException $ex)
//		{
//		}
			$this->enabled = false;
			
		if (!$this->enabled)
		{
			return;
		}
		
		$time = microtime();
		$time = explode(" ", $time);
		$time = $time[1] + $time[0];
		$this->endTime = $time;
		
		$this->endMemory = memory_get_usage();
		$this->peakMemory = memory_get_peak_usage();
		
		if (is_null($this->Sql))
		{
			$profile = new ProfileResource();
		}
		else
		{
			$profile = new ProfileSQL();
			$profile->setSql($this->Sql);
			$args = serialize($this->SqlArgs);
			$profile->setArgs($args);
			$profile->setHash(md5($this->Sql . $args));
		}
		
		$profile->setRef(Profiler::getRequestRef());
		$profile->setStartTime($this->startTime);
		$profile->setEndTime($this->endTime);
		$profile->setLoadTime($this->endTime - $this->startTime);
		$profile->setMemUsage($this->endMemory - $this->startMemory);
		$profile->setPeakMemUsage($this->peakMemory);
		$profile->setMachineId(Profiler::getMachineId());
		
		Dao::$ProfileInProgress = true;
		Dao::save($profile);
		Dao::$ProfileInProgress = false;
	}
	
	/**
	 * Get the IP address or host name of the machine
	 *
	 * @return string
	 */
	static function getMachineId()
	{
		// This exists on Windows XP, check it first because our dev boxes just return the loopback IP
		if (getenv('COMPUTERNAME') !== false)
		{
			return getenv('COMPUTERNAME');
		}
		
		// This should exist on Linux
		if (getenv('HOSTNAME') !== false)
		{
			return getenv('HOSTNAME');
		}
		
		// Exists on the Linux Apache web servers
		if (isset($_SERVER) && isset($_SERVER['SERVER_ADDR']))
		{
			return $_SERVER['SERVER_ADDR'];
		}
		
		return 'Unknown';
	}
	
	/**
	 * Get the url, hostname or IP of the request if available
	 *
	 * @return string
	 */
	static function getRequestRef()
	{
		if (isset($_SERVER))
		{
			$ref = '';
			
			if (isset($_SERVER['SERVER_NAME']))
			{
				$ref .= $_SERVER['SERVER_NAME'];
			}
			
			if (isset($_SERVER['PHP_SELF']))
			{
				$ref .= $_SERVER['PHP_SELF'];
			}
			
			return $ref;
		}
		
		return self::getMachineId();
	}
}

?>