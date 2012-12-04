<?php

$incpaths = array(
	get_include_path(),
);

set_include_path(implode(PATH_SEPARATOR, $incpaths));
class SystemCore
{
	public static function autoload($className)
	{
		$base = dirname(__FILE__);
		$autoloadPaths = array(
			$base . '/Dao/',
			$base . '/Dao/Config/',
	        $base . '/Dao/Database/',
			$base . '/Entity/',
			$base . '/Entity/Accounting/',
			$base . '/Entity/Personnel/',
			$base . '/Exception/',
			$base . '/Services/',
			$base . '/Utils/',
			$base . '/'
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

spl_autoload_register(array('SystemCore','autoload'));


// Bootstrap the Prado framework
require_once dirname(__FILE__) . '/Framework/prado.php';

?>