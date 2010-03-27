<?php
require(dirname(__FILE__) . '/bootstrap.php');



session_start();  
if(isset($_SERVER["REQUEST_URI"]) && trim($_SERVER["REQUEST_URI"])!="/")
{
	if(strstr($_SERVER["REQUEST_URI"],"/post/"))
	{
		$vars = explode("/",$_SERVER["REQUEST_URI"]);
		$serviceName = $vars[2];
		$method = $vars[3];
		
		try
		{
			$service = new $serviceName();
		}
		catch(Exception $e)
		{
			die("Service $service don't exsits!");
		}
		
		try
		{
			echo $service->$method($_POST);
		}
		catch(Exception $e)
		{
			die("Error calling method $method on service $serviceName: ".$e->getMessage());
		}
	}
	else
	{
		if(System::getUser() instanceof UserAccount)
		{
			$vars = explode("/",$_SERVER["REQUEST_URI"]);
			echo WapInterface::$vars[1]($vars);
		}
		else 
			header("Location: /");
	}
}
else 
{
	echo WapInterface::defaultPage(!System::getUser() instanceof UserAccount);
}

?>