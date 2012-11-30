<?php
class AppService
{
	public function run()
	{
		//authenticate the user
		if(!isset($_REQUEST["user"]))
			throw new Exception("Invalid user info!");
		$this->_authenticateUser($_REQUEST["user"]);
			
		$methods = explode(".", (isset($_REQUEST["method"]) ? trim($_REQUEST["method"]) : ''));
		$serviceName = isset($methods[0]) ? trim($methods[0]) : '';
		$func = isset($methods[1]) ? trim($methods[1]) : '';
			
		if(!isset($serviceName) || $serviceName === '')
			throw new Exception("Empty Service!");
		if (!class_exists($serviceName = "App" . ucfirst($serviceName) . "Service"))
			throw new Exception("Service Unknown!");
		$service = new $serviceName();
			
		if(!isset($func) || $func === '')
			throw new Exception("Empty Function!");
		if (!method_exists($service,$func))
			throw new Exception("Requested Method Unknown!");
			
		return $service->$func($_REQUEST);
	}
	
	private function _authenticateUser($userParam)
	{
		$userService = new UserAccountService();
		$userAccount = $userService->getUserByUsernameAndPassword(isset($userParam['username']) ? $userParam['username'] : '', isset($userParam['password']) ? $userParam['password'] : '', false, false);
		if(!$userAccount instanceof UserAccount)
			throw new Exception("Invalid UserAccount!");
	
		Core::setUser($userAccount);
	}
}
