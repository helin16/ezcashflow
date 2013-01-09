<?php
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
			$base . '/Entity/Property/',
			$base . '/Exception/',
			$base . '/Services/',
			$base . '/Utils/',
			$base . '/'
		);
		foreach ($autoloadPaths as $path)
		{
			if (file_exists($path . $className . '.php'))
			{
				require_once $path . $className . '.php';
				return true;
			}
		}
		return false;
	}
}

spl_autoload_register(array('SystemCore','autoload'));


// Bootstrap the Prado framework
require_once dirname(__FILE__) . '/Framework/prado.php';

?>