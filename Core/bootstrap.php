<?php

$incpaths = array(
	get_include_path(),
	dirname(__FILE__),
	dirname(__FILE__) . "/Framework/"
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
	        $base . '/Dao/Entity/',
			$base . '/Dao/Map/',
			$base . '/Dao/Statements/',
			$base . '/Dao/Statements/lib/',
			$base . '/Dao/Tools/',
			$base . '/Dao/Tools/lib/',
			$base . '/Dao/Types/',
			$base . '/Entity/',
			$base . '/Entity/AssetServer/',
			$base . '/Entity/Content/',
			$base . '/Entity/Profiler/',
			$base . '/Entity/Newsletter/',
			$base . '/Entity/Banner/',
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