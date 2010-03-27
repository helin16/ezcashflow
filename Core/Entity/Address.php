<?php

class Address extends ProjectEntity
{
	/**
	 * getter Line1
	 *
	 * @return String
	 */
	public function getLine1()
	{
		return $this->Line1;
	}
	
	/**
	 * Setter Line1
	 *
	 * @param String Line1
	 */
	public function setLine1($Line1)
	{
		$this->Line1 = $Line1;
	}	
	
	/**
	 * getter Line2
	 *
	 * @return String
	 */
	public function getLine2()
	{
		return $this->Line2;
	}
	
	/**
	 * Setter Line2
	 *
	 * @param String Line2
	 */
	public function setLine2($Line2)
	{
		$this->Line2 = $Line2;
	}
	
	/**
	 * getter Suburb
	 *
	 * @return String
	 */
	public function getSuburb()
	{
		return $this->Suburb;
	}
	
	/**
	 * Setter Suburb
	 *
	 * @param String Suburb
	 */
	public function setSuburb($Suburb)
	{
		$this->Suburb = $Suburb;
	}

	/**
	 * getter State
	 *
	 * @return State
	 */
	public function getState()
	{
		return $this->State;
	}
	
	/**
	 * Setter State
	 *
	 * @param State State
	 */
	public function setState($State)
	{
		$this->State = $State;
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
		
	/**
	 * getter PostCode
	 *
	 * @return String
	 */
	public function getPostCode()
	{
		return $this->PostCode;
	}
	
	/**
	 * Setter PostCode
	 *
	 * @param String PostCode
	 */
	public function setPostCode($PostCode)
	{
		$this->PostCode = $PostCode;
	}
	
	public function __toString()
	{
		return $this->getLine1()." ".$this->getLine2()." ".$this->getSuburb()." ".$this->getState()." ".$this->getCountry()." ".$this->getPostCode();
	}
	
	protected function __meta()
	{
		parent::__meta();

		Map::setField($this,new TString("Line1"));
		Map::setField($this,new TString("Line2"));
		Map::setField($this,new TString("Suburb"));
		Map::setField($this,new TString("PostCode"));
		Map::setField($this,new ManyToOne("State","State"));
		Map::setField($this,new ManyToOne("Country","Country"));
	}	
}

?>