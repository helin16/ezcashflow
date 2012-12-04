<?php
/**
 * Country Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Country extends HydraEntity
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
	 * @see HydraEntity::__toString()
	 */
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
	 * @see HydraEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'c');
		
		DaoMap::setStringType('name','varchar');
		DaoMap::setOneToMany("states","State","st");
		DaoMap::commit();
	}
	
}
?>