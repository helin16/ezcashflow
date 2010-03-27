<?php
class System
{
	public static function getUser()
	{
		$useraccount = unserialize($_SESSION['currentUser']);
		return $useraccount instanceof UserAccount ? $useraccount : null;
	}
	
	public static function setUser($useraccount)
	{
		$_SESSION['currentUser']=serialize($useraccount);
	}
	
	public static function getSessionVar($sessionName)
	{
		return $_SESSION[$sessionName];
	}
}
?>