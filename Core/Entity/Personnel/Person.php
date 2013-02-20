<?php
/**
 * Person Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Person extends BaseEntityAbstract
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
	public function getUserAccounts()
	{
	    $this->loadOneToMany('userAccounts');
	    return $this->userAccounts;
	}
	/**
	 * Setter UserAccount
	 *
	 * @param array $userAccounts The useraccounts
	 * 
	 * @return Person
	 */
	public function setUserAccounts(array $userAccounts)
	{
		$this->userAccounts = $userAccounts;
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
	    $names = array();
	    if(($firstName = trim($this->getFirstName())) !== '')
	        $names[] = $firstName;
	    if(($lastName = trim($this->getLastName())) !== '')
	        $names[] = $lastName;
		return trim(implode(' ', $names));
	}
	/**
	 * Getting the default address of this person
	 * 
	 * @return Address
	 */
	public function getAddress()
	{
	    $dao = new EntityDao('Address');
	    $addresses = $dao->findByCriteria('addr.id = (select x.addressId from x_person_address x where x.personId = ? order by x.isDefault desc limit 1)', array($this->getId()), 1, 1);
	    return isset($addresses[0]) ? $addresses[0] : null; 
	}
	/**
	 * getting the account entry for json
	 *
	 * @throws EntityException
	 */
	public function getJsonArray()
	{
	   $array = $this->_getJsonFromPM();
	   return $array;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__toString()
	 */
	public function __toString()
	{
	    if(($name = $this->getFullName()) !== '')
	        return $name;
	    return parent::__toString();
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'p');
		DaoMap::setStringType('firstName');
		DaoMap::setStringType('lastName');
		DaoMap::setOneToMany('userAccounts', 'UserAccount', 'ua');
		parent::loadDaoMap();
		DaoMap::commit();
	}
}

?>