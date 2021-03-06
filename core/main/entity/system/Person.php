<?php
/**
 * Person Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Person extends BaseEntityAbstract
{
    /**
     * The email
     *
     * @var string
     */
    private $email;
    /**
     * The first name of the user
     *
     * @var string
     */
    private $firstName;
    /**
     * The last name of the user
     *
     * @var string
     */
    private $lastName;
    /**
     * The fullname of the person
     *
     * @var string
     */
    private $fullName;
    /**
     * Getter for firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
    /**
     * Setter for firstName
     *
     * @param string $value The firstName
     *
     * @return Person
     */
    public function setFirstName($value)
    {
        $this->firstName = $value;
        return $this;
    }
    /**
     * Getter for lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }
    /**
     * Setter for lastName
     *
     * @param string $value The lastName
     *
     * @return Person
     */
    public function setLastName($value)
    {
        $this->lastName = $value;
        return $this;
    }
    /**
     * Getter for email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    /**
     * Setter for email
     *
     * @param string $value The email
     *
     * @return Person
     */
    public function setEmail($value)
    {
        $this->email = $value;
        return $this;
    }
    /**
     * Getter for fullname
     *
     * @return string
     */
    public function getFullName()
    {
    	if(trim($this->fullName) === '')
    		return trim(trim($this->getFirstName()) . ' ' . trim($this->getLastName()));
        return $this->fullName;
    }
    /**
     * Setter for fullName
     *
     * @param string $value The fullName
     *
     * @return Person
     */
    public function setFullName($value)
    {
        $this->fullName = $value;
        return $this;
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
     * Adding a role to the person
     *
     * @param Organization  $org
     * @param Role          $role
     * @param OrgPersonRole $rel
     *
     * @return Person
     */
    public function addRole(Organization $org, Role $role, OrgPersonRole &$rel = null)
    {
        $rel = OrgPersonRole::create($this, $org, $role);
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__toString()
     */
    public function __toString()
    {
        return $this->getEmail();
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntityAbstract::preSave()
     */
    public function preSave()
    {
    	if(trim($this->getEmail()) === '')
    		throw new EntityException('Email can NOT be empty', 'exception_entity_person_email_empty');
    	$this->setFullName(trim(trim($this->getFirstName()) . ' ' . trim($this->getLastName())));
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'p');
        DaoMap::setStringType('email', 'varchar', 100);
        DaoMap::setStringType('firstName', 'varchar', 50);
        DaoMap::setStringType('lastName', 'varchar', 50);
        DaoMap::setStringType('fullName', 'varchar', 200);
        parent::__loadDaoMap();

        DaoMap::createIndex('email');
        DaoMap::createIndex('firstName');
        DaoMap::createIndex('lastName');
        DaoMap::createIndex('fullName');
        DaoMap::commit();
    }
    /**
     * creating a perosn
     *
     * @param unknown $firstName
     * @param unknown $lastName
     * @param unknown $email
     *
     * @return Person
     */
    public static function create($firstName, $lastName, $email)
    {
    	$entity = new Person();
    	return $entity->setFirstName(trim($firstName))
    		->setLastName(trim($lastName))
    		->setEmail($email)
    		->save()
    		->addLog(Log::TYPE_SYS, 'Person (' . $firstName . ' ' . $lastName . ') created now with an email address: ' . $email);
    }
}

?>
