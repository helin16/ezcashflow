<?php
/**
 * State Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class State extends BaseEntityAbstract
{
    /**
     * The name of the state
     * 
     * @var string
     */
	private $name;
	/**
	 * The country
	 * 
	 * @var Country
	 */
	protected $country;
	/**
	 * getter Name
	 *
	 * @return string
	 */
	public function getName()
	{
	    return $this->name;
	}
	/**
	 * getter Name
	 * 
	 * @param string $Name The name of the state
	 *
	 * @return State
	 */
	public function setName($Name)
	{
		$this->name = $Name;
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__toString()
	 */
	public function __toString()
	{
		$return = $this->getName();
		if(($country = $this->getCountry()) instanceof Country)
		    $return .= ' (' . $country->getName() . ') ';
		return $return;
	}
	/**
	 * getter Country
	 *
	 * @return Country
	 */
	public function getCountry()
	{
	    $this->loadManyToOne('country');
		return $this->country;
	}
	/**
	 * Setter Country
	 *
	 * @param Country Country
	 * 
	 * @return State
	 */
	public function setCountry(Country $Country)
	{
		$this->country = $Country;
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::getJsonArray()
	 */
	public function getJsonArray()
	{
	    $state = array();
	    $state['id'] = $this->getId();
	    $state['name'] = $this->getName();
	    $state['country'] = $this->getCountry()->getJsonArray();
	    return $state;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'st');
		DaoMap::setStringType('name', 'varchar');
		DaoMap::setManyToOne("country", "Country", "c");
		parent::loadDaoMap();
		DaoMap::commit();
	}
	
}
?>