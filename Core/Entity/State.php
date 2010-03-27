<?php

class State extends ProjectEntity
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
	 * getter Country
	 *
	 * @return Country
	 */
	public function getCountry()
	{
		return $this->Country;
	}
	
	/**
	 * Setter Country
	 *
	 * @param Country Country
	 */
	public function setCountry($Country)
	{
		$this->Country = $Country;
	}
	
	protected function __meta()
	{
		parent::__meta();

		Map::setField($this,new TString("Name"));
		Map::setField($this,new ManyToOne("Country","Country"));
	}	
	
}
?>