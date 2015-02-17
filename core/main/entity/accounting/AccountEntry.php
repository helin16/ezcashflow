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
     * The account no of the account
     *
     * @var int
     */
    private $accountNo;
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
   	 * Getter for accountNo
   	 *
   	 * @return int
   	 */
   	public function getAccountNo()
   	{
   	    return $this->accountNo;
   	}
   	/**
   	 * Setter for accountNo
   	 *
   	 * @param int $value The accountNo
   	 *
   	 * @return AccountEntry
   	 */
   	public function setAccountNo($value)
   	{
   	    $this->accountNo = $value;
   	    return $this;
   	}
    /**
     * Getting the BreadCrumbs of the AccountEntry from the path
     *
     * @return array
     */
    public function getBreadCrumbs()
    {
    	$parentIds = array_filter($this->getPaths());
    	if(count($parentIds) === 0 || count($accounts = AccountEntry::getAllByCriteria('id in (' . implode(', ', array_fill(0, count($parentIds), '?')) . ')', $parentIds, false)) === 0)
    		return array();
		$map = array();
		foreach($accounts as $account)
			$map[$account->getId()] = $account->getName();
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
	    	if($this->getParent() instanceof AccountEntry) {
	    		if(intval($this->getParent()->getIsSumAcc()) === 0)
	    			throw new EntityException('You can ONLY create an account under a summary account.');
	    		$this->setRoot($this->getParent()->getRoot())
	    			->setType($this->getParent()->getType())
	    			->setOrganization($this->getParent()->getOrganization());
	    	}
    	}

    	$where ='accountNo = ? and organizationId = ?';
    	$params = array(trim($this->getAccountNo()), $this->getOrganization()->getId());
    	if(trim($this->getId()) !== '') {
			$where .= ' AND id != ?';
	    	$params[] = trim($this->getId());
    	}
    	if(self::countByCriteria($where, $params) > 0)
    		throw new EntityException('There is such an accountNo already: ' . $this->getAccountNo());
    	if(substr(trim($this->getAccountNo()), 0, 1) !== trim($this->getType()->getId()))
    		throw new EntityException('The account number should start with: '. $this->getType()->getId() . ', but got: ' . $this->getAccountNo());
    	if(intval($this->getIsSumAcc()) === true && Transaction::countByCriteria('accountEntryId = ?', array($this->getId())) > 0)
    		throw new EntityException('There are transactions against this account, it can NOT be a Summary Account!');
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntityAbstract::postSave()
     */
    public function postSave()
    {
    	if(!$this->getRoot() instanceof AccountEntry) {
    		$fakeEntry = new AccountEntry();
    		$fakeEntry->setProxyMode(true)
    			->setId($this->getId());
    		$this->setRoot($fakeEntry)
    			->setPath(trim($this->getId()))
    			->save();
    	}
    	if(trim($this->getPath()) === '') {
    		$path = (trim($this->getParent()->getPath()) . self::PATH_SEPARATOR . trim($this->getId()));
    		self::updateByCriteria('path = ?', 'id = ?', array($path, $this->getId()));
    	}
    }
    /**
     * The running balance
     *
     * @return number
     */
    public function getSumValue($resetCache = false)
    {
    	$sum = $this->getInitValue();
    	if(trim($this->getId()) === '')
    		return $sum;
    	$key = md5('SumValue' . $this->getId());
    	if(self::getCache($key) && $resetCache !== true)
    		return self::getCache($key);

		$transactions = Transaction::getAllByCriteria('accountEntryId = ?', array($this->getId()));
		foreach($transactions as $trans)
			$sum += $trans->getValue();
		$childrenAccounts = self::getAllByCriteria('parentId = ?', array($this->getId()));
		foreach($childrenAccounts as $acc)
			$sum += $acc->getSumValue();
		self::addCache($key, $sum);
		return $sum;
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
    		$array['sumValue'] = $this->getSumValue();
    	}
    	return parent::getJson($array, $reset);
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
    	DaoMap::begin($this, 'acc_entry');
    	DaoMap::setStringType('name', 'varchar', 100);
    	DaoMap::setBoolType('isSumAcc');
    	DaoMap::setIntType('accountNo', 'int', 10);
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
    	DaoMap::createIndex('accountNo');
    	DaoMap::createIndex('isSumAcc');
    	DaoMap::commit();
    }
    /**
     * Getting the accountentry by accounting code
     *
     * @param Organization $org
     * @param string       $accountNo
     *
     * @return Ambigous <NULL, BaseEntityAbstract>
     */
    public static function getAccountByCode(Organization $org, $accountNo)
    {
		$accounts = self::getAllByCriteria('accountNo = ? and organizationId = ?', array(trim($accountNo), trim($org->getId())), true, 1, 1);
		return (count($accounts) > 0 ? $accounts[0] : null);
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
    public static function createRootAccount(Organization $org, $name, AccountType $type, $isSumAcc = true, $initValue = 0, $description = '', $accountNo = '')
    {
    	$item = new AccountEntry();
    	return $item->setName(trim($name))
    		->setOrganization($org)
    		->setType($type)
    		->setInitValue($initValue)
    		->setIsSumAcc($isSumAcc)
    		->setDescription(trim($description))
    		->setAccountNo(trim($accountNo))
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
    public static function create(Organization $org, AccountEntry $parent, $name, $isSumAcc = false, $initValue = 0, $description = '', $accountNo = '')
    {
    	$item = new AccountEntry();
    	return $item->setName(trim($name))
    		->setOrganization($org)
    		->setParent($parent)
    		->setInitValue($initValue)
    		->setIsSumAcc($isSumAcc)
    		->setDescription(trim($description))
    		->setType($parent->getType())
    		->setAccountNo(trim($accountNo))
    		->save();
    }
}

?>
