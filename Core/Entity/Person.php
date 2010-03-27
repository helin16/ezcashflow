<?php

class Person extends ProjectEntity
{
	/**
	 * getter UserAccount
	 *
	 * @return UserAccount
	 */
	public function getUserAccount()
	{
		return $this->UserAccount;
	}
	
	/**
	 * Setter UserAccount
	 *
	 * @param UserAccount UserAccount
	 */
	public function setUserAccount($UserAccount)
	{
		$this->UserAccount = $UserAccount;
	}
	
	/**
	 * getter FirstName
	 *
	 * @return String
	 */
	public function getFirstName()
	{
		return $this->FirstName;
	}
	
	/**
	 * Setter FirstName
	 *
	 * @param String FirstName
	 */
	public function setFirstName($FirstName)
	{
		$this->FirstName = $FirstName;
	}
	
	/**
	 * getter LastName
	 *
	 * @return String
	 */
	public function getLastName()
	{
		return $this->LastName;
	}
	
	/**
	 * Setter LastName
	 *
	 * @param String LastName
	 */
	public function setLastName($LastName)
	{
		$this->LastName = $LastName;
	}
	
	public function getFullName()
	{
		return $this->getFirstName()." ".$this->getLastName();
	}
		
	protected function __toString()
	{
		return $this->getFirstName()." ".$this->getLastName();
	}
	
	protected function __meta()
	{
		parent::__meta();

		Map::setField($this,new TString("FirstName"));
		Map::setField($this,new TString("LastName"));
		
		Map::setField($this,new OneToOne("UserAccount","UserAccount","Person"));
	}	
}

?>