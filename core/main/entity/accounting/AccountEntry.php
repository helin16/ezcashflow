<?php
/**
 * AccountEntry Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class AccountEntry extends BaseEntityAbstract
{
	const PATH_SEPARATOR = ',';
    /**
     * The username
     *
     * @var string
     */
    private $name;
    /**
     * Organization
     * 
     * @var Organization
     */
    protected $organization;
    /**
     * The initValue of the account
     * 
     * @var double
     */
    private $initValue = 0;
    /**
     * The Root accountEntry of this account
     * 
     * @var AccountEntry
     */
    protected $root = null;
    /**
     * The parent accountEntry of this account
     * 
     * @var AccountEntry
     */
    protected $parent = null;
    /**
     * The path of this account
     * 
     * @var string
     */
    private $path = '';
    /**
     * The description of this account
     * 
     * @var string
     */
    private $description = '';
    /**
     * Getter for name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Setter for name
     *
     * @param string $value The name
     *
     * @return AccountEntry
     */
    public function setName($value) 
    {
        $this->name = $value;
        return $this;
    }
    /**
     * Setter for Organization
     *
     * @param Organization $value The Organization
     *
     * @return AccountEntry
     */
    public function setOrganization($value)
    {
    	$this->organization = $value;
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
     * Getter for initValue
     *
     * @return double
     */
    public function getInitValue() 
    {
        return $this->initValue;
    }
    /**
     * Setter for initValue
     *
     * @param double $value The initValue
     *
     * @return AccountEntry
     */
    public function setInitValue($value) 
    {
        $this->initValue = $value;
        return $this;
    }
    /**
     * Getter for root
     *
     * @return AccountEntry
     */
    public function getRoot() 
    {
    	$this->loadManyToOne('root');
        return $this->root;
    }
    /**
     * Setter for root
     *
     * @param AccountEntry $value The root
     *
     * @return AccountEntry
     */
    public function setRoot(AccountEntry $value = null) 
    {
        $this->root = $value;
        return $this;
    }
    /**
     * Getter for Parent
     *
     * @return AccountEntry
     */
    public function getParent() 
    {
    	$this->loadManyToOne('parent');
        return $this->parent;
    }
    /**
     * Setter for Parent
     *
     * @param AccountEntry $value The Parent
     *
     * @return AccountEntry
     */
    public function setParent(AccountEntry $value = null) 
    {
        $this->parent = $value;
        return $this;
    }
    /**
     * Getter for path
     *
     * @return AccountEntry
     */
    public function getPath() 
    {
        return $this->path;
    }
    /**
     * Setter for path
     *
     * @param string $value The path
     *
     * @return AccountEntry
     */
    public function setPath($value) 
    {
        $this->path = $value;
        return $this;
    }
    /**
     * Getter for description
     *
     * @return AccountEntry
     */
    public function getDescription() 
    {
        return $this->description;
    }
    /**
     * Setter for description
     *
     * @param string $value The description
     *
     * @return AccountEntry
     */
    public function setDescription($value) 
    {
        $this->description = $value;
        return $this;
    }
    /**
     * getting the path in an array
     * 
     * @return multitype:int
     */
    public function getPaths()
    {
    	return explode(self::PATH_SEPARATOR, $this->getPath);
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntityAbstract::postSave()
     */
    public function postSave()
    {
    	if($this->getParent() instanceof AccountEntry) {
    		$this->setRoot($this->getParent()->getRoot())
    			->setPath(trim($this->getParent()->getPath()) . self::PATH_SEPARATOR , trim($this->getId()))
    			->save();
    	} else {
    		$fakeEntry = new AccountEntry();
    		$fakeEntry->setProxyMode(true)
    			->setId($this->getId());
    		$this->setRoot($fakeEntry)
	    		->setPath(trim($this->getId()))
	    		->save();
    	}
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
    	DaoMap::begin($this, 'acc_entry');
    	DaoMap::setStringType('name', 'varchar', 100);
    	DaoMap::setManyToOne('organization', "Organization", 'acc_entry_org');
    	DaoMap::setIntType('initValue', 'double', '10,4', false);
    	DaoMap::setManyToOne('root', "AccountEntry", 'acc_entry_root', true);
    	DaoMap::setManyToOne('parent', "AccountEntry", 'acc_entry_parent', true);
    	DaoMap::setStringType('path', 'varchar', 255);
    	DaoMap::setStringType('description', 'varchar', 255);
    	parent::__loadDaoMap();
    
    	DaoMap::createIndex('name');
    	DaoMap::createIndex('initValue');
    	DaoMap::createIndex('path');
    	DaoMap::commit();
    }
    /**
     * Creating a RootAccount
     * 
     * @param string $name
     * 
     * @return Ambigous <BaseEntityAbstract, GenericDAO>
     */
    public function createRootAccount($name)
    {
    	$item = new AccountEntry();
    	return $item->setName(trim($name))
    		->save();
    }
    /**
     * Creating a AccountEntry
     * 
     * @param AccountEntry $parent
     * @param string       $name
     * @param double       $initValue
     * @param string       $description
     * 
     * @return Ambigous <BaseEntityAbstract, GenericDAO>
     */
    public function create(AccountEntry $parent, $name, $initValue = 0, $description = '')
    {
    	$item = new AccountEntry();
    	return $item->setName(trim($name))
    		->setParent($parent)
    		->setInitValue($initValue)
    		->setDescription(trim($description))
    		->save();
    }
}

?>
