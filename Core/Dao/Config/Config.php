<?php

class Config
{
	private static $values = null;
	
	public static function get($service,$name)
	{
		if(self::$values == null)
			self::$values = require_once('defaultConfig.php');
			
		if(isset(self::$values[$service]) && isset(self::$values[$service][$name]))
			return self::$values[$service][$name];
		else
			throw new Exception("Service($service)/Name($name) not defined in config");
	}
}

?>