<?php

class Address extends HydraEntity
{
	private $line1;
	private $line2;
	private $suburb;
	private $postCode;
	/**
	 * @var State
	 */
	protected $state;
	/**
	 * @var Country
	 */
	protected $country;
	
	/**
	 * getter Line1
	 *
	 * @return String
	 */
	public function getLine1()
	{
		return $this->line1;
	}
	
	/**
	 * Setter Line1
	 *
	 * @param String Line1
	 */
	public function setLine1($Line1)
	{
		$this->line1 = $Line1;
	}	
	
	/**
	 * getter Line2
	 *
	 * @return String
	 */
	public function getLine2()
	{
		return $this->line2;
	}
	
	/**
	 * Setter Line2
	 *
	 * @param String Line2
	 */
	public function setLine2($Line2)
	{
		$this->line2 = $Line2;
	}
	
	/**
	 * getter Suburb
	 *
	 * @return String
	 */
	public function getSuburb()
	{
		return $this->suburb;
	}
	
	/**
	 * Setter Suburb
	 *
	 * @param String Suburb
	 */
	public function setSuburb($Suburb)
	{
		$this->suburb = $Suburb;
	}

	/**
	 * getter State
	 *
	 * @return State
	 */
	public function getState()
	{
		$this->loadManyToOne("state");
		return $this->state;
	}
	
	/**
	 * Setter State
	 *
	 * @param State State
	 */
	public function setState($State)
	{
		$this->state = $State;
	}
	
	/**
	 * getter Country
	 *
	 * @return Country
	 */
	public function getCountry()
	{
		$this->loadManyToOne("country");
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
		
	/**
	 * getter PostCode
	 *
	 * @return String
	 */
	public function getPostCode()
	{
		return $this->postCode;
	}
	
	/**
	 * Setter PostCode
	 *
	 * @param String PostCode
	 */
	public function setPostCode($PostCode)
	{
		$this->postCode = $PostCode;
	}
	
	public function __toString()
	{
		$line = trim($this->getLine1()." ".$this->getLine2());
		return ($line==""? "" : ", ").$this->getSuburb().", ".$this->getState().", ".$this->getCountry()." ".$this->getPostCode();
	}
	
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'addr');
		
		DaoMap::setStringType('line1','varchar',255);
		DaoMap::setStringType('line2','varchar');
		DaoMap::setStringType('suburb','varchar');
		DaoMap::setStringType('postCode','varchar');
		DaoMap::setManyToOne("state","State","st");
		DaoMap::setManyToOne("country","Country","con");
		DaoMap::commit();
	}
}

?>