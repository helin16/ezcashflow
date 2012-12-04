<?php
class X_Person_Address extends HydraEntity 
{
	private $isDefault;
	
	protected $person;
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
	 * @var Person
	 */
	public function setPerson($Person)
	{
		$this->person = $Person;
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
	 * @var Address
	 */
	public function setAddress($Address)
	{
		$this->address = $Address;
	}
	
	/**
	 * getter IsDefault
	 *
	 * @return IsDefault
	 */
	public function getIsDefault()
	{
		return $this->isDefault;
	}
	
	/**
	 * setter IsDefault
	 *
	 * @var IsDefault
	 */
	public function setIsDefault($IsDefault)
	{
		$this->isDefault = $IsDefault;
	}
	
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'xpa');
		
		DaoMap::setBoolType('isDefault');
		DaoMap::setManyToOne("person","Person","p");
		DaoMap::setManyToOne("adress","Adress","addr");
		DaoMap::commit();
	}
}
?>