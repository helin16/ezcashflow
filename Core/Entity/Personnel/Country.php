<?php
/**
 * Country Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Country extends BaseEntityAbstract
{
    /**
     * the name of the country
     * @var string
     */
	private $name;
	/**
	 * The states
	 * 
	 * @var array
	 */
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
	 * @param string Name The name of the country
	 * 
	 * @return Country
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
	    if(($name = trim($this->getName())) !== '')
		    return $name;
	    return parent::__toString();
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
	 * @param States[] States The states that belongs to this country
	 * 
	 * @return Country
	 */
	public function setStates(array $States)
	{
		$this->states = $States;
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::getJsonArray()
	 */
	public function getJsonArray()
	{
	    $country = array();
	    $country['id'] = $this->getId();
	    $country['name'] = $this->getName();
	    return $country;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'c');
		DaoMap::setStringType('name', 'varchar');
		DaoMap::setOneToMany("states", "State", "st");
		parent::loadDaoMap();
		DaoMap::commit();
	}
	
}
?>