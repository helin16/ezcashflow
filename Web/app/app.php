<?php
class App
{
	public static function autoload($className)
	{
		$base = dirname(__FILE__);
		$autoloadPaths = array(
			$base . '/service/',
			$base . '/service/User/',
			$base . '/service/Trans/',
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
	
	public static function run()
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
		file_put_contents('/tmp/test.json', $return, FILE_APPEND);
		echo $return;
	}
	
	private static function _getJson($result, $errors = array())
	{
		return json_encode(array('result' => $result, 'errors' => $errors));
	}
}

spl_autoload_register(array('App','autoload'));
require dirname(__FILE__).'/../bootstrap.php';
App::run();
