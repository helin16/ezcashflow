<?php
/**
 * The app server end
 *
 * @package    App
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 */
class AppService
{
    /**
     * The main execution function
     * 
     * @return array
     * @throws Exception
     */
	public function run()
	{
		//authenticate the user
		if(!isset($_REQUEST["user"]))
			throw new Exception("Invalid user info!");
		$this->_authenticateUser($_REQUEST["user"]);
			
		list($serviceName, $func) = explode(".", (isset($_REQUEST["method"]) ? trim($_REQUEST["method"]) : ''));
		if (!class_exists($serviceName = "App" . ucfirst($serviceName) . "Service"))
			throw new Exception("Service Unknown!");
		$service = new $serviceName();
		if (!method_exists($service, $func))
			throw new Exception("Requested Method Unknown!");
		return $service->$func($_REQUEST);
	}
	/**
	 * authenticateUser the current user
	 * 
	 * @param array $userParam The user information
	 * 
	 * @return AppService
	 * @throws Exception
	 */
	private function _authenticateUser($userParam)
	{
		$userAccount = BaseService::getInstance('UserAccountService')->getUserByUsernameAndPassword(isset($userParam['username']) ? $userParam['username'] : '', isset($userParam['password']) ? $userParam['password'] : '', false, false);
		if(!$userAccount instanceof UserAccount)
			throw new Exception("Invalid UserAccount!");
		Core::setUser($userAccount);
		return $this;
	}
}
