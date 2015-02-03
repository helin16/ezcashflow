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
     * Whether this is a summary account. if it is, it can't be have any transactions against them
     *
     * @var bool
     */
    private $isSumAcc = false;
    /**
     * The type of the account
     *
     * @var AccountType
     */
    protected $type;
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
    	return explode(self::PATH_SEPARATOR, $this->getPath());
    }
    /**
     * Getting the BreadCrumbs of the AccountEntry from the path
     *
     * @return array
     */
    public function getBreadCrumbs()
    {
    	$parentIds = $this->getPaths();
    	if(count($parentIds) === 0 || count($accounts = AccountEntry::getAllByCriteria('id in (' . implode(', ', array_fill(0, count($parentIds), '?')) . ')', $parentIds, false)) === 0)
    		return array();
		$map = array();
		foreach($accounts as $account)
			$map[$account->getId()] = $account;
		$names = array();
		foreach($parentIds as $id)
			$names[] = $map[$id];
		return $names;
    }
    /**
     * Getter for isSumAcc
     *
     * @return bool
     */
    public function getIsSumAcc()
    {
        return $this->isSumAcc;
    }
    /**
     * Setter for isSumAcc
     *
     * @param bool $value The isSumAcc
     *
     * @return AccountEntry
     */
    public function setIsSumAcc($value)
    {
        $this->isSumAcc = $value;
        return $this;
    }
    /**
     * Getter for type
     *
     * @return AccountType
     */
    public function getType()
    {
    	$this->loadManyToOne('type');
        return $this->type;
    }
    /**
     * Setter for type
     *
     * @param AccountEntry $value The type
     *
     * @return AccountEntry
     */
    public function setType($value)
    {
        $this->type = $value;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntityAbstract::preSave()
     */
    public function preSave()
    {
    	if(trim($this->getId()) !== '') {
    		if(count(self::countByCriteria('parentId = ?', array($this->getId()))) > 0) {
    			$this->setIsSumAcc(true);
    		}
    	} else {
    		if($this->getParent() instanceof AccountEntry && !$this->getParent()->getIsSumAcc())
    			throw new EntityException('You can ONLY create an account under a summary account.');
    	}
    }
	/**
     * (non-PHPdoc)
     * @see BaseEntityAbstract::getJson()
     */
    public function getJson($extra = '', $reset = false)
    {
    	$array = array();
    	if(!$this->isJsonLoaded($reset))
    	{
    		$array['breadCrumbs'] = $this->getBreadCrumbs();
    		$array['parent'] = $this->getParent() instanceof AccountEntry ? $this->getParent()->getJson() : null;
    		$array['type'] = $this->getType() instanceof AccountType ? $this->getType()->getJson() : null;
    	}
    	return parent::getJson($array, $reset);
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
    	DaoMap::setManyToOne('type', 'AccountType', 'acc_entry_type');
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
     * @param Organization $org
     * @param string       $name
     * @param AccountType  $type
     * @param number       $initValue
     * @param string       $description
     *
     * @return Ambigous <BaseEntityAbstract, GenericDAO>
     */
    public static function createRootAccount(Organization $org, $name, AccountType $type, $initValue = 0, $description = '')
    {
    	$item = new AccountEntry();
    	return $item->setName(trim($name))
    		->setOrganization($org)
    		->setType($type)
    		->setInitValue($initValue)
    		->setDescription($description)
    		->save();
    }
    /**
     * Creating a AccountEntry
     *
     * @param Organization $org
     * @param AccountEntry $parent
     * @param string       $name
     * @param number       $initValue
     * @param string       $description
     *
     * @return Ambigous <BaseEntityAbstract, GenericDAO>
     */
    public static function create(Organization $org, AccountEntry $parent, $name, $initValue = 0, $description = '')
    {
    	$item = new AccountEntry();
    	return $item->setName(trim($name))
    		->setOrganization($org)
    		->setParent($parent)
    		->setInitValue($initValue)
    		->setDescription(trim($description))
    		->setType($parent->getType())
    		->save();
    }
}

?>
