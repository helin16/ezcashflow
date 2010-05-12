<?php

/**
 * Global core settings and operations
 *
 * @package Core
 * @subpackage Utils
 */
class Core
{
	/**
	 * @var UserAccount
	 */
	private static $userAccount = null;
	
	/**
	 * @var Role
	 */
	private static $role = null;
	

	public static function setRole(Role $role)
	{
		self::setUser(self::getUser(),$role);
	}
	
	/**
	 * Set the active user on the core for auditing purposes
	 *
	 * @param UserAccount $userAccount
	 */
	public static function setUser($userAccount, Role $role=null)
	{
		self::$userAccount = $userAccount;
		self::$role = $role;
	}
	
	/**
	 * Get the current user set against the System for auditing purposes
	 *
	 * @return UserAccount
	 */
	public static function getUser()
	{
		return self::$userAccount;
	}
	
	/**
	 * Get the current user role set against the System for Dao filtering purposes
	 *
	 * @return Role
	 */
	public static function getRole()
	{
		if (!is_null(self::$role))
		{
			return self::$role;
		}
	}

	public static function serialize()
	{
		$array = array(	self::$userAccount,
						self::$role);
		//Debug::inspect($array);
		//die; 
		return serialize($array);
		
	}
	
	public static function unserialize($string)
	{
		list($ua,$r) = unserialize($string);
		$array = array($ua,$r); 
//		Debug::inspect($array);
//		die;
		Core::setUser($ua,$r);
	}
	
	public static function inspect($data)
	{
		echo "<pre>";
		var_dump($data);
		echo "</pre>";
	}
	
	/**
	 * @return PageLanguage
	 */
	public static function getPageLanguage()
	{
		$code = isset($_SESSION["language"]) ? $_SESSION["language"] : "en";
		$service = new LanguageService();
		return $service->findByCode($code);
	}
}

?>