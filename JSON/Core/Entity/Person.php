<?php

class Person extends HydraEntity
{
	private $firstName;
	private $lastName;
	protected $userAccounts;
	/**
	 * getter UserAccount
	 *
	 * @return UserAccount
	 */
	public function getUserAccount()
	{
		return $this->userAccounts;
	}
	
	/**
	 * Setter UserAccount
	 *
	 * @param UserAccount UserAccount
	 */
	public function setUserAccount($UserAccount)
	{
		$this->userAccounts = $UserAccount;
	}
	
	/**
	 * getter FirstName
	 *
	 * @return String
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}
	
	/**
	 * Setter FirstName
	 *
	 * @param String FirstName
	 */
	public function setFirstName($FirstName)
	{
		$this->firstName = $FirstName;
	}
	
	/**
	 * getter LastName
	 *
	 * @return String
	 */
	public function getLastName()
	{
		return $this->lastName;
	}
	
	/**
	 * Setter LastName
	 *
	 * @param String LastName
	 */
	public function setLastName($LastName)
	{
		$this->lastName = $LastName;
	}
	
	public function getFullName()
	{
		return $this->getFirstName()." ".$this->getLastName();
	}
		
	public function __toString()
	{
		return $this->getFirstName()." ".$this->getLastName();
	}
	
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'r');
		
		DaoMap::setStringType('firstName');
		DaoMap::setStringType('lastName');
		DaoMap::setOneToMany("userAccounts","UserAccount","ua");
		DaoMap::commit();
	}
}

?>