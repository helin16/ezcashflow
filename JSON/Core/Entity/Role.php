<?php
class Role extends HydraEntity 
{
	private $name;
	protected $userAccounts;
	/**
	 * getter Name
	 *
	 * @return Name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * setter Name
	 *
	 * @var Name
	 */
	public function setName($Name)
	{
		$this->name = $Name;
	}
	
	/**
	 * getter UserAccounts
	 *
	 * @return UserAccounts
	 */
	public function getUserAccounts()
	{
		return $this->userAccounts;
	}
	
	/**
	 * setter UserAccounts
	 *
	 * @var UserAccounts
	 */
	public function setUserAccounts($UserAccounts)
	{
		$this->userAccounts = $UserAccounts;
	}
	
	
	public function __toString()
	{
		return $this->getName();
	}
	
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'r');
		
		DaoMap::setStringType('name','varchar');
		DaoMap::setManyToMany("userAccounts","UserAccount",DaoMap::RIGHT_SIDE,"ua");
		DaoMap::commit();
	}
}
?>