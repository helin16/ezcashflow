<?php

class Country extends ProjectEntity
{
	/**
	 * getter Name
	 *
	 * @return Name
	 */
	public function getName()
	{
		return $this->Name;
	}
	
	/**
	 * Setter Name
	 *
	 * @param Name Name
	 */
	public function setName($Name)
	{
		$this->Name = $Name;
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
		return $this->States;
	}
	
	/**
	 * Setter States
	 *
	 * @param States States
	 */
	public function setStates($States)
	{
		$this->States = $States;
	}
	
	protected function __meta()
	{
		parent::__meta();

		Map::setField($this,new TString("Name"));
		Map::setField($this,new OneToMany("States","State"));
	}	
	
}
?>