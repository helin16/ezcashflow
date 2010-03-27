<?php
class X_Person_Address extends ProjectEntity 
{
	/**
	 * getter Person
	 *
	 * @return Person
	 */
	public function getPerson()
	{
		return $this->Person;
	}
	
	/**
	 * setter Person
	 *
	 * @var Person
	 */
	public function setPerson($Person)
	{
		$this->Person = $Person;
	}
	
	/**
	 * getter Address
	 *
	 * @return Address
	 */
	public function getAddress()
	{
		return $this->Address;
	}
	
	/**
	 * setter Address
	 *
	 * @var Address
	 */
	public function setAddress($Address)
	{
		$this->Address = $Address;
	}
	
	/**
	 * getter IsDefault
	 *
	 * @return IsDefault
	 */
	public function getIsDefault()
	{
		return $this->IsDefault;
	}
	
	/**
	 * setter IsDefault
	 *
	 * @var IsDefault
	 */
	public function setIsDefault($IsDefault)
	{
		$this->IsDefault = $IsDefault;
	}
	
	protected function __meta()
	{
		parent::__meta();
		Map::setField($this,new TInt('IsDefault',1,1));
		
		Map::setField($this,new ManyToOne("Person","Person"));
		Map::setField($this,new ManyToOne("Adress","Adress"));
	}
}
?>