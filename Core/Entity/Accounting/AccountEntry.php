<?php
/**
 * Account Entry Entity
 * 
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class AccountEntry extends BaseEntityAbstract
{
    /**
     * how many digits of the account number
     * @var int
     */
    const ACC_NO_LENGTH = 4;
    /**
     * The default separator for breadCrubms
     * @var string
     */
    const BREADCRUMBS_SEPARATOR = ' / ';
    /**
     * The account type for ASSET account
     * @var int
     */
    const TYPE_ASSET = 1;
    /**
     * The account type for LIABILITY account
     * @var int
     */
    const TYPE_LIABILITY = 2;
    /**
     * The account type for INCOME account
     * @var int
     */
    const TYPE_INCOME = 3;
    /**
     * The account type for EXPENSE account
     * @var int
     */
    const TYPE_EXPENSE = 4;
    /**
     * The name of the account
     * 
     * @var string
     */
	private $name;
	/**
	 * The sum value of this account
	 * 
	 * @var float
	 */
	private $sum = '0.00';
    /**
     * The account number of the account
     * 
     * @var string
     */
	private $accountNumber;
    /**
     * The comments of the account
     * 
     * @var string
     */
	private $comments;
    /**
     * The vable of the account
     * 
     * @var string
     */
	private $value;
    /**
     * The budget of the account
     * 
     * @var string
     */
	private $budget;
	/**
	 * The root account of the current account
	 * 
	 * @var AccountEntry
	 */
	protected $root;
	/**
	 * The direct of the current account
	 * 
	 * @var AccountEntry
	 */
	protected $parent;
	/**
	 * Whether we are allow transactions created on this account, most likely to be the leaf account
	 * 
	 * @var bool
	 */
	private $allowTrans = false;
	/**
	 * getter name
	 *
	 * @return name
	 */
	public function getName()
	{
		return $this->name;
	}
	/**
	 * setter name
	 *
	 * @param string name The name of the account
	 * 
	 * @return AccountEntry
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}
	/**
	 * getter accountNumber
	 *
	 * @return accountNumber
	 */
	public function getAccountNumber()
	{
		return $this->accountNumber;
	}
	/**
	 * setter accountNumber
	 *
	 * @param string accountNumber The account number
	 * 
	 * @return AccountEntry
	 */
	public function setAccountNumber($accountNumber)
	{
		$this->accountNumber = $accountNumber;
		return $this;
	}
	/**
	 * getter comments
	 *
	 * @return comments
	 */
	public function getComments()
	{
		return $this->comments;
	}
	/**
	 * setter comments
	 *
	 * @param string comments The comments
	 * 
	 * @return AccountEntry
	 */
	public function setComments($comments)
	{
		$this->comments = $comments;
		return $this;
	}
	/**
	 * getter value
	 *
	 * @return value
	 */
	public function getValue()
	{
		return $this->value;
	}
	/**
	 * setter value
	 * 
	 * @param string $value The new value
	 * 
	 * @return AccountEntry
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}
	/**
	 * getter parent
	 *
	 * @return AccountEntry
	 */
	public function getParent()
	{
		$this->loadManyToOne("parent");
		return $this->parent;
	}
	/**
	 * setter parent
	 * 
	 * @param AccountEntry $parent The new parent
	 * 
	 * @return AccountEntry
	 */
	public function setParent(AccountEntry $parent = null)
	{
		$this->parent = $parent;
		return $this;
	}
	/**
	 * getter root
	 *
	 * @return AccountEntry
	 */
	public function getRoot()
	{
		$this->loadManyToOne("root");
		return $this->root;
	}
	/**
	 * setter root
	 *
	 * @param AccountEntry root The root of the current account entry
	 * 
	 * @return AccountEntry
	 */
	public function setRoot(AccountEntry $root = null)
	{
		$this->root = $root;
		return $this;
	}
	/**
	 * getter budget
	 *
	 * @return budget
	 */
	public function getBudget()
	{
		return $this->budget;
	}
	/**
	 * setter budget
	 *
	 * @param string budget The new budget of the current account
	 * 
	 * @return AccountEntry
	 */
	public function setBudget($budget)
	{
		$this->budget = $budget;
		return $this;
	}
	/**
	 * Getting the sum of the values of the current account entry
	 * 
	 * @return number
	 */
	public function getSum()
	{
	    return $this->calSum();
	}
	/**
	 * Setter for the sum
	 * 
	 * @param float $sum The sum value
	 * 
	 * @return AccountEntry
	 */
	public function setSum($sum)
	{
	    $this->sum = $sum;
	    return $this;
	}
	/**
	 * Getting the NextAccountNo for the new children of the provided parent accountentry
	 * 
	 * @throws ServiceException
	 * @return int
	 */
	public function getNextAccountNo()
	{
	    $parentAccountNumber = $this->getAccountNumber();
	    $sql="select accountNumber from accountentry where active = 1 and accountNumber like '" . $parentAccountNumber . str_repeat('_', AccountEntry::ACC_NO_LENGTH). "' order by accountNumber asc";
	    $result = Dao::getResultsNative($sql);
	    if(count($result) === 0)
	        return $parentAccountNumber . str_repeat('0', AccountEntry::ACC_NO_LENGTH);
	    
	    $expectedAccountNos = array_map(create_function('$a', 'return "' . $parentAccountNumber . '".str_pad($a, ' . AccountEntry::ACC_NO_LENGTH . ', 0, STR_PAD_LEFT);'), range(0, str_repeat('9', AccountEntry::ACC_NO_LENGTH)));
	    $usedAccountNos = array_map(create_function('$a', 'return $a["accountNumber"];'), $result);
	    $unUsed = array_diff($expectedAccountNos, $usedAccountNos);
	    sort($unUsed);
	    if (count($unUsed) === 0)
	        throw new ServiceException("account number over loaded (parentId = " . $this->getId() . ", parentAccNo = $parentAccountNumber)!");
	    
	    return $unUsed[0];
	}
	/**
	 * Calculate the sum value
	 * 
	 * @return float
	 */
	public function calSum()
	{
	    $sql = "select distinct id, value from accountentry where rootId = ? and accountNumber like ? and active = 1";
	    $result = Dao::getResultsNative($sql, array($this->getRoot()->getId(), $this->getAccountNumber() . '%'));
	    $accIds = array_map(create_function('$a', 'return $a["id"];'), $result);
	    $accValues = array_map(create_function('$a', 'return $a["value"];'), $result);
	    $accIds_string = implode(', ', $accIds);
	    $value = array_sum($accValues);
	     
	    $sql = "select sum(t.value) `sum` from transaction t inner join accountentry acc on (acc.id = t.fromId and acc.id in (" . $accIds_string . ") and acc.active = 1) where t.active = 1";
	    $result = Dao::getSingleResultNative($sql, array());
	    $out = $result === false ? 0 : $result['sum'];
	    $sql = "select sum(t.value) `sum` from transaction t inner join accountentry acc on (acc.id = t.toId and acc.id in (" . $accIds_string . ") and acc.active = 1) where t.active = 1";
	    $result = Dao::getSingleResultNative($sql, array());
	    $in = $result === false ? 0 : $result['sum'];
	    return round($value + $in - $out, 2);
	}
	/**
	 * Getting all the children accounts for the current account
	 * 
	 * @param bool $inclSelf Whether to include it own value
	 * 
	 * @return Ambigous <array(BaseEntity), BaseEntity, multitype:, string, multitype:multitype: >
	 */
	public function getChildren($includeSelf = false, $directChildrenOnly = true, $pageNumber = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array())
	{
	    if($directChildrenOnly === true)
	    {
	        $where = 'parentId = :id';
	        $params = array('id' => $this->getId());
	        if($includeSelf === true)
	            $where .= ' or id = :id ';
	    }
		else
		{
		    $where = 'accountNumber like :accNo';
		    $params = array('accNo' => $this->getAccountNumber() . "%");
		    if($includeSelf === false)
		    {
		        $where .= ' AND id != :id ';
    		    $params['id'] = $this->getId();
		    }
		}
        return EntityDao::getInstance(get_class($this))->findByCriteria($where, $params, $pageNumber, $pageSize, $orderBy);
	}
	/**
	 * Getting a snapshot of the current account
	 * 
	 * @return string
	 */
	public function getLongshot()
	{
		return $this->getBreadCrumbs() . " - $" . $this->getSum();
	}
	/**
	 * Getting the BreadCrumbs of the current account path
	 * 
	 * @param bool   $inclSelf  Whether to include it own value
	 * @param bool   $forId     Displaying the breadcrumbs for ids
	 * @param string $separator The separator of the breadcrumbs
	 * 
	 * @return string
	 */
	public function getBreadCrumbs($inclSelf = true, $forId = false, $separator = self::BREADCRUMBS_SEPARATOR)
	{
	    $requestedNos = $this->_getParentsAccNo($inclSelf);
	    $sql = "select " . ($forId === true ? 'id' : 'name') . ' `id` from accountentry where rootId = ? and accountNumber in (' . "'" . implode("', '", $requestedNos) . "'" . ') order by accountNumber asc';
	    $result = Dao::getResultsNative($sql, array($this->getRoot()->getId()));
		return implode($separator, array_map(create_function('$a', 'return $a["id"];'), $result));
	}
	/**
	 * Getting all the parent accounts
	 * 
	 * @param bool $inclSelf Whether to include it own value
	 * 
	 * @return multitype:AccountEntry unknown
	 */
	public function getParents($inclSelf = false, $pageNumber = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array("accountNumber" => "asc"))
	{
	    $requestedNos = $this->_getParentsAccNo($inclSelf);
		return EntityDao::getInstance(get_class($this))->findByCriteria('rootId = ? and accountNumber in (' . "'" . implode("', '", $requestedNos) . "'" . ')', array($this->getRoot()->getId()), $pageNumber, $pageSize, $orderBy);
	}
	/**
	 * Getting all parents' account number
	 * 
	 * @param bool $inclSelf Whether to include it own value
	 * 
	 * @throws EntityException
	 */
	private function _getParentsAccNo($inclSelf = false)
	{
	    $root = $this->getRoot();
	    $rootAccNo = $root->getAccountNumber();
	    $accountNo = $this->getAccountNumber();
	    if(((strlen($accountNo) -  strlen($rootAccNo)) % AccountEntry::ACC_NO_LENGTH) !== 0)
	        throw new EntityException('Account Entry(ID=' . $this->getId() . ') has invalid account no:' . $accountNo);
	    
	    $requestedNos = array($accountNo);
	    for($i = 1, $levels = ((strlen($accountNo) -  strlen($rootAccNo)) / AccountEntry::ACC_NO_LENGTH); $i <= $levels; $i++)
	    {
	        $requestedNos[] = substr($accountNo, 0, (0 - AccountEntry::ACC_NO_LENGTH) * $i);
	    }
	    if($inclSelf !== true)
	        $requestedNos = array_filter($requestedNos, create_function('$a', 'return $a == ' . $accountNo . ';'));
	    return $requestedNos;
	}
	/**
	 * The setter for allow trans
	 * 
	 * @param bool $allowTrans Whether we are allowing transaction on this accountentry
	 * 
	 * @return AccountEntry
	 */
	public function setAllowTrans($allowTrans)
	{
	    $this->allowTrans = $allowTrans;
	    return $this;
	}
	/**
	 * Getter for allowTrans
	 * 
	 * @return bool
	 */
	public function getAllowTrans()
	{
	    return $this->allowTrans;
	}
	/**
	 * getting the account entry for json
	 * 
	 * @param bool $loadParent whether to load the parent array as well. Just to avoid looping
	 * 
	 * @return multitype:boolean NULL multitype: unknown
	 */
	public function getJsonArray()
	{
	    $acc = $this->_getJsonFromPM();
	    $thisNo = $this->getAccountNumber();
	    $acc['level'] = ceil((strlen($thisNo) - 1) / 4);
	    $acc['breadCrumbs'] = array(
	        'id' => $this->getBreadCrumbs(true, true),
	    	'name' => $this->getBreadCrumbs(true, false)
	    );
	    $acc['sum'] = $this->getSum();
	    $acc['children'] = array_map(create_function('$a', 'return $a->getId();'), $this->getChildren(false, true, null, DaoQuery::DEFAUTL_PAGE_SIZE, array('accountNumber' => 'asc')));
	    $acc['parent'] = array();
	    if(($parent = $this->getParent()) instanceof AccountEntry)
    	    $acc['parent'] = array('id' => $parent->getId(), 'name' => $parent->getName());
	    $acc['root'] = array();
	    if(($root = $this->getRoot()) instanceof AccountEntry)
    	    $acc['root'] = array('id' => $root->getId(), 'name' => $root->getName());
	    return $acc;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__toString()
	 */
	public function __toString()
	{
		return $this->getName();
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'etr');
		DaoMap::setStringType('name', 'varchar', 255);
		DaoMap::setIntType("accountNumber", "int", 41);
		DaoMap::setStringType('comments', 'varchar', 255);
		DaoMap::setStringType('value', 'varchar');
		DaoMap::setStringType('budget', 'varchar');
		DaoMap::setManyToOne("parent", "AccountEntry", "petr", true);
		DaoMap::setManyToOne("root", "AccountEntry", "petrr");
		DaoMap::setBoolType("allowTrans");
		parent::loadDaoMap();
		DaoMap::commit();
	}
}

?>