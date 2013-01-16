<?php
/**
 * Address Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Address extends BaseEntityAbstract
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
	public function setState(State $State)
	{
		$this->state = $State;
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
	 * @see BaseEntityAbstract::getJsonArray()
	 */
	public function getJsonArray()
	{
	    $addr = array();
	    $addr['id'] = $this->getId();
	    $addr['line1'] = $this->getLine1();
	    $addr['line2'] = $this->getLine2();
	    $addr['suburb'] = $this->getSuburb();
	    $addr['postcode'] = $this->getPostCode();
	    $addr['state'] = $this->getState()->getJsonArray();
	    $addr['full'] = $this->__toString();
	    return $addr;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__toString()
	 */
	public function __toString()
	{
    	$string = ($line1 = trim($this->getLine1())) === '' ? '' :  $line1;
    	$string .= ($line2 = trim($this->getLine2())) === '' ? '' :  ', ' . $line2;
    	$string .= ($suburb = trim($this->getSuburb())) === '' ? '' :  ', ' . $suburb;
    	if(($state = $this->getState()) instanceof State)
    	{
        	$string .= (($statename = trim($state->getName())) === '') ? '' :  ', ' . $statename;
        	$string .= (!($country = $state->getCountry()) instanceof Country || ($countryname = trim($country->getName())) === '') ? '' :  ', ' . $countryname;
    	}
    	$string .= ($postcode = trim($this->getPostCode())) === '' ? '' :  ' ' . $postcode;
        if(trim($string) !== '')
            return $string;            
        return parent::__toString();
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'addr');
		DaoMap::setStringType('line1', 'varchar', 255);
		DaoMap::setStringType('line2', 'varchar');
		DaoMap::setStringType('suburb', 'varchar');
		DaoMap::setStringType('postCode', 'varchar');
		DaoMap::setManyToOne("state", "State", "st");
		parent::loadDaoMap();
		DaoMap::commit();
	}
}

?>