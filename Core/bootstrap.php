<?php
/**
 * Boostrapper for the Core module
 * 
 * @package    Core
 * @author lhe
 */
abstract class SystemCoreAbstract
{
    /**
     * autoloading function
     * 
     * @param string $className The class that we are trying to autoloading
     * 
     * @return boolean Whether we loaded the class
     */
	public static function autoload($className)
	{
		$base = dirname(__FILE__);
		$autoloadPaths = array(
			$base . '/Dao/',
			$base . '/Dao/Config/',
	        $base . '/Dao/Database/',
			$base . '/Entity/',
			$base . '/Entity/Accounting/',
			$base . '/Entity/Assets/',
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
spl_autoload_register(array('SystemCoreAbstract','autoload'));
// Bootstrap the Prado framework
require_once dirname(__FILE__) . '/Framework/prado.php';

?>