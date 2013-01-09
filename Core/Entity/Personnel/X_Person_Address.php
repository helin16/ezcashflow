<?php
/**
 * X_Person_Address Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class X_Person_Address extends BaseEntityAbstract 
{
    /**
     * Whether this is the default address for that person
     * 
     * @var bool
     */
	private $isDefault;
	/**
	 * The person
	 * 
	 * @var Person
	 */
	protected $person;
	/**
	 * The address
	 * 
	 * @var Address
	 */
	protected $address;
	/**
	 * getter Person
	 *
	 * @return Person
	 */
	public function getPerson()
	{
		return $this->person;
	}
	/**
	 * setter Person
	 * 
	 * @param Person $Person The person
	 * 
	 * @return X_Person_Address
	 */
	public function setPerson(Person $Person)
	{
		$this->person = $Person;
		return $this;
	}
	/**
	 * getter Address
	 *
	 * @return Address
	 */
	public function getAddress()
	{
		return $this->address;
	}
	/**
	 * setter Address
	 * 
	 * @param Address $Address The address
	 * 
	 * @return X_Person_Address
	 */
	public function setAddress(Address $Address)
	{
		$this->address = $Address;
		return $this;
	}
	/**
	 * getter IsDefault
	 *
	 * @return bool
	 */
	public function getIsDefault()
	{
		return $this->isDefault;
	}
	/**
	 * setter IsDefault
	 * 
	 * @param bool $IsDefault Whether this is a default address
	 * 
	 * @return X_Person_Address
	 */
	public function setIsDefault($IsDefault)
	{
		$this->isDefault = $IsDefault;
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'xpa');
		DaoMap::setBoolType('isDefault');
		DaoMap::setManyToOne("person", "Person", "p");
		DaoMap::setManyToOne("address", "Address", "addr");
		parent::loadDaoMap();
		DaoMap::commit();
	}
}
?>