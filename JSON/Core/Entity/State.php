<?php

class State extends HydraEntity
{
	private $name;
	protected $country;
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
	 * Setter Name
	 *
	 * @param Name Name
	 */
	public function setName($Name)
	{
		$this->name = $Name;
	}
	
	public function __toString()
	{
		return $this->getName();
	}
	
	/**
	 * getter Country
	 *
	 * @return Country
	 */
	public function getCountry()
	{
		return $this->country;
	}
	
	/**
	 * Setter Country
	 *
	 * @param Country Country
	 */
	public function setCountry($Country)
	{
		$this->country = $Country;
	}
	
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'st');
		
		DaoMap::setStringType('name','varchar');
		DaoMap::setManyToOne("country","Country","c");
		DaoMap::commit();
	}
	
}
?>