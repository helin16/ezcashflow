<?php
/**
 * Role Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Role extends BaseEntityAbstract 
{
    /**
     * The name of the role
     * @var string
     */
    private $name;
    /**
     * The useraccounts of the person
     * @var array
     */
	protected $userAccounts;
	/**
	 * getter Name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	/**
	 * setter Name
	 * 
	 * @param string $Name The name of the role
	 * 
	 * @return Role
	 */
	public function setName($Name)
	{
		$this->name = $Name;
		return $this;
	}
	/**
	 * getter UserAccounts
	 *
	 * @return array
	 */
	public function getUserAccounts()
	{
		return $this->userAccounts;
	}
	/**
	 * setter UserAccounts
	 *
	 * @param array $UserAccounts The useraccounts linked to that role
	 * 
	 * @return Role
	 */
	public function setUserAccounts(array $UserAccounts)
	{
		$this->userAccounts = $UserAccounts;
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::getJsonArray()
	 */
	public function getJsonArray()
	{
		$role = $this->_getJsonFromPM();
		return $role;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__toString()
	 */
	public function __toString()
	{
		if(($name = trim($this->getName())) !== '')
		    return $name;
	    return parent::__toString();
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'r');
		DaoMap::setStringType('name', 'varchar');
		DaoMap::setManyToMany("userAccounts", "UserAccount", DaoMap::RIGHT_SIDE, "ua");
		parent::loadDaoMap();
		DaoMap::commit();
	}
}
?>