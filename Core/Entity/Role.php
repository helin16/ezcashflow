<?php
class Role extends ProjectEntity 
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
	 * setter Name
	 *
	 * @var Name
	 */
	public function setName($Name)
	{
		$this->Name = $Name;
	}
	
	/**
	 * getter UserAccounts
	 *
	 * @return UserAccounts
	 */
	public function getUserAccounts()
	{
		return $this->UserAccounts;
	}
	
	/**
	 * setter UserAccounts
	 *
	 * @var UserAccounts
	 */
	public function setUserAccounts($UserAccounts)
	{
		$this->UserAccounts = $UserAccounts;
	}
	
	
	protected function __toString()
	{
		return $this->getName();
	}
	
	protected function __meta()
	{
		parent::__meta();

		Map::setField($this,new TString("Name"));
		Map::setField($this,new ManyToMany('UserAccounts',"UserAccount","Roles",ManyToMany::RIGHT));
		
		Map::setEagerFields($this,array('Role.UserAccounts'));		
	}
}
?>