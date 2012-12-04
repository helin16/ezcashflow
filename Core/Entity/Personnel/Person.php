<?php
/**
 * Person Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Person extends HydraEntity
{
    /**
     * The firstname of the person
     * @var string
     */
	private $firstName;
    /**
     * The lastname of the person
     * @var string
     */
	private $lastName;
    /**
     * The useraccounts of the person
     * @var array
     */
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
	 * @param array $UserAccounts The useraccounts
	 * 
	 * @return Person
	 */
	public function setUserAccount(array $UserAccounts)
	{
		$this->userAccounts = $UserAccount;
		return $this;
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
	 * @param String FirstName The firstName of the person
	 * 
	 * @return Person
	 */
	public function setFirstName($FirstName)
	{
		$this->firstName = $FirstName;
		return $this;
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
	 * @param String $LastName The last name
	 * 
	 * @return Person
	 */
	public function setLastName($LastName)
	{
		$this->lastName = $LastName;
		return $this;
	}
	/**
	 * getting the fullname of the person
	 * 
	 * @return string
	 */
	public function getFullName()
	{
		return $this->getFirstName()." ".$this->getLastName();
	}
	/**
	 * (non-PHPdoc)
	 * @see HydraEntity::__toString()
	 */
	public function __toString()
	{
		return $this->getFirstName()." ".$this->getLastName();
	}
	/**
	 * (non-PHPdoc)
	 * @see HydraEntity::__loadDaoMap()
	 */
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