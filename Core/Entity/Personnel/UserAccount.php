<?php

class UserAccount extends HydraEntity 
{
	private $userName;
	private $password;
	
	protected $person;
	protected $roles;
	
	/**
	 * getter UserName
	 *
	 * @return String
	 */
	public function getUserName()
	{
		return $this->userName;
	}
	
	/**
	 * Setter UserName
	 *
	 * @param String UserName
	 */
	public function setUserName($UserName)
	{
		$this->userName = $UserName;
	}
	
	/**
	 * getter Password
	 *
	 * @return String
	 */
	public function getPassword()
	{
		return $this->password;
	}
	
	/**
	 * Setter Password
	 *
	 * @param String Password
	 */
	public function setPassword($Password)
	{
		$this->password = $Password;
	}
	
	/**
	 * getter Person
	 *
	 * @return Person
	 */
	public function getPerson()
	{
		$this->loadManyToOne("person");
		return $this->person;
	}
	
	/**
	 * Setter Person
	 *
	 * @param Person Person
	 */
	public function setPerson($Person)
	{
		$this->person = $Person;
	}
	
	/**
	 * getter Roles
	 *
	 * @return Roles
	 */
	public function getRoles()
	{
		$this->loadManyToMany("roles");
		return $this->roles;
	}
	
	/**
	 * setter Roles
	 *
	 * @var Roles
	 */
	public function setRoles($Roles)
	{
		$this->roles = $Roles;
	}
	
	public function __toString()
	{
		return $this->getUserName();
	}
		

	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'ua');
		
		DaoMap::setStringType('userName','varchar',256);
		DaoMap::setStringType('password','varchar',256);
		DaoMap::setOneToOne("person","Person",true,"p");
		DaoMap::setManyToMany("roles","Role",DaoMap::LEFT_SIDE,"r",false);
		DaoMap::commit();
	}
	
}

?>