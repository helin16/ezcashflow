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
	const ACCOUNT_NO_LEVEL_DIGIS = 2;
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
    public function getBreadCrumbs($resetCache = false)
    {
    	$accounts = $this->getParents(true, $resetCache);
    	$names = array();
		foreach($accounts as $account)
			$names[] = $account->getName();
		return $names;
    }
    /**
     * Getting all the parents
     *
     * @return array
     */
    public function getParents($incSelf = false, $resetCache = false)
    {
    	$key = md5('Parents_' . ($incSelf === true ? '1' : '0') . '_' . $this->getId());
    	if(self::cacheExsits($key) === true && $resetCache !== true)
    		return self::getCache($key);

    	$parentIds = $this->getPaths();
    	if($incSelf === false)
    		$parentIds = array_filter($parentIds, create_function('$a', 'return trim($a) !== "" && $a !== ' . $this->getId() . ';'));
    	else
    		$parentIds = array_filter($parentIds);
    	if(count($parentIds) === 0)
    		$value = array();
    	else {
	    	$map = array();
	    	foreach(self::getAllByCriteria('id in (' . implode(', ', array_fill(0, count($parentIds), '?')) . ')', $parentIds, false) as $account)
	    		$map[$account->getId()] = $account;
	    	$value = array();
	    	foreach($parentIds as $id)
	    		$value[] = $map[$id];
    	}
    	self::addCache($key, $value);
    	return $value;
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
    		if(self::countByCriteria('parentId = ?', array($this->getId())) > 0) {
    			$this->setIsSumAcc(true);
    		}
    	} else {
	    	if($this->getParent() instanceof AccountEntry) {
	    		if(intval($this->getParent()->getIsSumAcc()) === 0)
	    			throw new EntityException('You can ONLY create an account under a summary account, but Account(' . implode(' / ', $this->getParent()->getBreadCrumbs()) . ') is NOT.');
	    		$this->setRoot($this->getParent()->getRoot())
	    			->setType($this->getParent()->getType())
	    			->setOrganization($this->getParent()->getOrganization());
	    	}
	    	if(trim($this->getAccountNo()) === '') {
	    		if($this->getParent() instanceof AccountEntry) {
	    			$count = self::countByCriteria('parentId = ?', array($this->getParent()->getId()));
	    			$this->setAccountNo($this->getParent()->getAccountNo() . str_pad($count + 1, self::ACCOUNT_NO_LEVEL_DIGIS, '0', STR_PAD_LEFT));
	    		} else {
	    			$this->setAccountNo($this->getType()->getId());
	    		}
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
    		$paths = $this->getParent()->getPaths();
    		$paths[] =  trim($this->getId());
    		$this->setPath(implode(self::PATH_SEPARATOR, $paths))
    			->save();
    	}
    }
    /**
     * Getting the Running Balance
     *
     * @return number
     */
    public function getRuningBalance($resetCache = false)
    {
    	$key = md5('RuningBalance_' . $this->getId());
    	if(self::cacheExsits($key) === true && $resetCache !== true)
    		return self::getCache($key);
    	$value = $this->getPeriodRuningBalance(Udate::zeroDate(), UDate::maxDate(), true, $resetCache);
    	self::addCache($key, $value);
    	return $value;
    }
    /**
     * The running balance
     *
     * @return number
     */
    public function getSumValue($resetCache = false)
    {
    	$key = md5('SumValue_' . $this->getId());
    	if(self::cacheExsits($key) === true && $resetCache !== true)
    		return self::getCache($key);

    	$sum = $this->getInitValue() + $this->getRuningBalance($resetCache);
    	$childrenAccounts = $this->getChildren(true, $resetCache);
    	foreach($childrenAccounts as $acc)
    		$sum += $acc->getInitValue();
    	self::addCache($key, $sum);
		return $sum;
    }
    /**
     * Getting the round balance for a period of time
     *
     * @param UDate $start
     * @param UDate $end
     * @param bool  $resetCache
     *
     * @return number
     */
    public function getPeriodRuningBalance(UDate $start, UDate $end, $inclChildren = false, $resetCache = false)
    {
    	if(trim($this->getId()) === '')
    		return 0;
    	$key = md5('PeriodRuningBalance_' . $this->getId() . trim($start) . trim($end) . trim($inclChildren));
    	if(self::cacheExsits($key) === true && $resetCache !== true)
    		return self::getCache($key);
		$sql = 'select sum(trans.value) `value` from transaction trans
				inner join accountentry acc on (acc.id = trans.accountEntryId and acc.active = 1 and trans.active = 1 and ' . ($inclChildren === true ? 'acc.path like "' . $this->getPath() . '%"' : 'acc.id = ' . $this->getId()) . ' and acc.organizationId = ' . Core::getOrganization()->getId() . ')
				where trans.logDate >= ? and trans.logDate <= ?';
    	$result = Dao::getResultsNative($sql, array(trim($start), trim($end)), PDO::FETCH_ASSOC);
    	$value = count($result) > 0 ? $result[0]['value'] : 0;
    	self::addCache($key, $value);
    	return $value;
    }
    /**
     * Getting all the children for an account entry
     *
     * @param bool $allChildren
     * @param bool $resetCache
     *
     * @return array
     */
    public function getChildren($allChildren = false, $resetCache = false)
    {
    	$key = md5('Children_' . ($allChildren === true ? '1' : '0') . '_' . trim($this->getId()));
    	if(self::cacheExsits($key) === true && $resetCache !== true)
    		return self::getCache($key);

    	$where = 'parentId = ?';
    	$params = array($this->getId());
    	if($allChildren === true) {
    		$where = 'path like ?';
    		$params = array(trim($this->getPath()) . ',%');
    	}
    	$value = self::getAllByCriteria($where, $params);
    	self::addCache($key, $value);
    	return $value;
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
    		$array['runingValue'] = $this->getRuningBalance($reset);
    		$array['sumValue'] = $this->getSumValue($reset);
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
    	DaoMap::setIntType('accountNo', 'int', 20);
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
