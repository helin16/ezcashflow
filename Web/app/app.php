<?php
/**
 * The app server end
 * 
 * @package    App
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 */
class App
{
    /**
     * autoload function for the spl_autoload_register()
     * @param unknown_type $className
     */
	public static function autoload($className)
	{
		$base = dirname(__FILE__);
		$autoloadPaths = array(
			$base . '/service/',
			$base . '/service/User/',
			$base . '/service/Trans/',
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
	/**
	 * The runner
	 */
	public static function run($doLog = false)
	{
		$results = $errors = array();
		try
		{
			$app = new AppService();
			$results = $app->run();
		}
		catch(Exception $e)
		{
			$errors[] = $e->getMessage();
		}
		$return = json_encode(array('errors'=>$errors, 'resultData' => $results));
		if($doLog === true)
		    file_put_contents('/tmp/test.json', $return, FILE_APPEND);
		echo $return;
	}
}

spl_autoload_register(array('App','autoload'));
require dirname(__FILE__).'/../bootstrap.php';
App::run();
