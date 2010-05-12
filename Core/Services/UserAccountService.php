<?php
class UserAccountService extends BaseService 
{
	public function __construct()
	{
		parent::__construct("UserAccount");
	}
	
	public function getUserByUsername($username,$silenceMode=true)
	{
		try
		{
			$userAccounts = $this->findByCriteria("`UserName`='$username'");
			if(count($userAccounts)==1)
				return $userAccounts[0];
			else if(count($userAccounts)>1)
				throw new AuthenticationException("Multiple Users Found!Contact you administrator!");
			else
				throw new AuthenticationException("No User Found!");
		}
		catch(Exception $e)
		{
			if($silenceMode!=true)
				throw $e;
			return null;
		}
	}
	
	public function getUserByUsernameAndPassword($username,$password,$silenceMode=false)
	{
		try
		{
			$userAccounts = $this->findByCriteria("`UserName`='$username' AND `Password`='".sha1($password)."'");
			if(count($userAccounts)==1)
				return $userAccounts[0];
			else if(count($userAccounts)>1)
				throw new AuthenticationException("Multiple Users Found!Contact you administrator!");
			else
				throw new AuthenticationException("No User Found!");
		}
		catch(Exception $e)
		{
			if($silenceMode!=true)
				throw $e;
			return null;
		}
	}
}
?>