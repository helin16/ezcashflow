<?php
class App
{
	public static function autoload($className)
	{
		$autoloadPaths = array(
			dirname(__FILE__) . '/service/'
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
		if(($method = isset($_REQUEST['method']) ? trim($_REQUEST['method']) : '') === '')
			throw new Exception("No method given");
		
		list($serviceName, $methodName) = explode(".", $_REQUEST['method']);
		
		$errors = $result = array();
		try
		{
			$service = new 	$serviceName();
			$result = $service->$methodName($_REQUEST);
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		return self::_getJson($result, $errors);
	}
	
	private static function _getJson($result, $errors = array())
	{
		return json_encode(array('result' => $result, 'errors' => $errors));
	}
}

spl_autoload_register(array('App','autoload'));

require(dirname(__FILE__) . '/../bootstrap.php');

App::run();