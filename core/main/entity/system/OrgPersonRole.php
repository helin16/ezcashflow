<?php
class OrgPersonRole extends BaseEntityAbstract
{
	/**
	 * The person
	 * 
	 * @var Person
	 */
	protected $person;
	/**
	 * The Role
	 * 
	 * @var Role
	 */
	protected $role;
	/**
	 * The organization
	 * 
	 * @var Organization
	 */
	protected $organization;
	/**
	 * Getter for person
	 *
	 * @return Person
	 */
	public function getPerson() 
	{
		$this->loadManyToOne('person');
	    return $this->person;
	}
	/**
	 * Setter for person
	 *
	 * @param Person $value The person
	 *
	 * @return OrgPersonRole
	 */
	public function setPerson(Person $value) 
	{
	    $this->person = $value;
	    return $this;
	}
	/**
	 * Getter for Role
	 *
	 * @return Role
	 */
	public function getRole() 
	{
		$this->loadManyToOne('role');
	    return $this->role;
	}
	/**
	 * Setter for Role
	 *
	 * @param Role $value The Role
	 *
	 * @return OrgPersonRole
	 */
	public function setRole(Role $value) 
	{
	    $this->role = $value;
	    return $this;
	}
	/**
	 * Getter for Organization
	 *
	 * @return Organization
	 */
	public function getOrganization() 
	{
		$this->loadManyToOne('organization');
	    return $this->organization;
	}
	/**
	 * Setter for Organization
	 *
	 * @param Organization $value The Organization
	 *
	 * @return OrgPersonRole
	 */
	public function setOrganization($value) 
	{
	    $this->organization = $value;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'org_per_role');
		DaoMap::setManyToOne('person', "Person", 'org_per_role_p');
		DaoMap::setManyToOne('role', "Role", 'org_per_role_r');
		DaoMap::setManyToOne('organization', "Organization", 'org_per_role_org');
		parent::__loadDaoMap();
	
		DaoMap::commit();
	}
}