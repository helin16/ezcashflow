<?php

$incpaths = array(
	dirname(__FILE__)
);

set_include_path(implode(PATH_SEPARATOR, $incpaths));
class Web
{
	public static function autoload($className)
	{
		$autoloadPaths = array(
			dirname(__FILE__) . '/Services/',
			dirname(__FILE__) . '/Interface/',
			dirname(__FILE__) . '/'
				);
		
		$found = false;
		
		foreach ($autoloadPaths as $path)
		{
			if (file_exists($path . $className . '.php'))
			{
				require_once $path . $className . '.php';
				$found = true;
				break;
			}
		}
		
		return $found;
	}
}

spl_autoload_register(array('Web','autoload'));

// Bootstrap the Hydra core for its autoloader settings
require(dirname(__FILE__) . '/../Core/bootstrap.php');

?>