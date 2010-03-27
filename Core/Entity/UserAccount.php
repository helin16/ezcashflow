<?php

class UserAccount extends ProjectEntity 
{
	/**
	 * getter UserName
	 *
	 * @return String
	 */
	public function getUserName()
	{
		return $this->UserName;
	}
	
	/**
	 * Setter UserName
	 *
	 * @param String UserName
	 */
	public function setUserName($UserName)
	{
		$this->UserName = $UserName;
	}
	
	/**
	 * getter Password
	 *
	 * @return String
	 */
	public function getPassword()
	{
		return $this->Password;
	}
	
	/**
	 * Setter Password
	 *
	 * @param String Password
	 */
	public function setPassword($Password)
	{
		$this->Password = $Password;
	}
	
	/**
	 * getter Person
	 *
	 * @return Person
	 */
	public function getPerson()
	{
		return $this->Person;
	}
	
	/**
	 * Setter Person
	 *
	 * @param Person Person
	 */
	public function setPerson($Person)
	{
		$this->Person = $Person;
	}
	
	/**
	 * getter Roles
	 *
	 * @return Roles
	 */
	public function getRoles()
	{
		return $this->Roles;
	}
	
	/**
	 * setter Roles
	 *
	 * @var Roles
	 */
	public function setRoles($Roles)
	{
		$this->Roles = $Roles;
	}
	
	protected function __toString()
	{
		return $this->getUserName();
	}
		
	protected function __meta()
	{
		parent::__meta();

		Map::setField($this,new TString("UserName"));
		Map::setField($this,new TString("Password",255));
		Map::setField($this,new OneToOneOwner('Person',"Person"));
		Map::setField($this,new ManyToMany("Roles","Role","UserAccounts"));
		
		Map::setEagerFields($this,array('UserAccount.Person'));		
	}		
	
}

?>