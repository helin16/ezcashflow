<?php
/**
 * Address Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Address extends HydraEntity
{
    /**
     * The first line of the address
     * 
     * @var string
     */
	private $line1;
    /**
     * The 2nd line of the address
     * 
     * @var string
     */
	private $line2;
	/**
	 * The suburb of the address
	 * 
	 * @var string
	 */
	private $suburb;
	/**
	 * The postcode of the address
	 * 
	 * @var string
	 */
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
	 * @param string Line1 The first line
	 * 
	 * @return Address
	 */
	public function setLine1($Line1)
	{
		$this->line1 = $Line1;
		return $this;
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
	 * @param String Line2 The 2nd line of the address.
	 * 
	 * @return Address
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
	 * @param String Suburb The new suburb
	 * 
	 * @return Address
	 */
	public function setSuburb($Suburb)
	{
		$this->suburb = $Suburb;
		return $this;
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
	 * @param State State The new State
	 * 
	 * @return Address
	 */
	public function setState(State $State = null)
	{
		$this->state = $State;
		return $this;
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
	 * @param Country Country The new country
	 * 
	 * @return Address
	 */
	public function setCountry(Country $Country = null)
	{
		$this->country = $Country;
		return $this;
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
	 * @param String PostCode The postcode of the suburb
	 * 
	 * @return Address
	 */
	public function setPostCode($PostCode)
	{
		$this->postCode = $PostCode;
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see HydraEntity::__toString()
	 */
	public function __toString()
	{
		$line = trim($this->getLine1()." ".$this->getLine2());
		return ($line==""? "" : ", ").$this->getSuburb().", ".$this->getState().", ".$this->getCountry()." ".$this->getPostCode();
	}
	/**
	 * (non-PHPdoc)
	 * @see HydraEntity::__loadDaoMap()
	 */
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