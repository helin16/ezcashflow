<?php

class Country extends HydraEntity
{
	private $name;
	protected $states;
	
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
	 * getter States
	 *
	 * @return States
	 */
	public function getStates()
	{
		$this->loadOneToMany("states");
		return $this->states;
	}
	
	/**
	 * Setter States
	 *
	 * @param States States
	 */
	public function setStates($States)
	{
		$this->states = $States;
	}
	
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'c');
		
		DaoMap::setStringType('name','varchar');
		DaoMap::setOneToMany("states","State","st");
		DaoMap::commit();
	}
	
}
?>