<?php
/**
 * Account Entry Entity
 * 
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class AccountEntry extends HydraEntity
{
    /**
     * The name of the account
     * 
     * @var string
     */
	private $name;
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
	 * @param bool $includeChildren Whether we need to calculate this for all its children
	 * @param bool $inclSelf        Whether to include it own value
	 * 
	 * @return number
	 */
	public function getSum($includeChildren=false, $inclSelf=true)
	{
		$sql = "select sum(t.value) from transaction t 
							where t.active =1 and t.fromId=".$this->getId();
		$result = Dao::getResultsNative($sql);
		$out = $result[0][0];
		$sql = "select sum(t.value) from transaction t 
							where t.active =1 and t.toId=".$this->getId();
		$result = Dao::getResultsNative($sql);
		$in = $result[0][0];
		return $this->getValue() + $in - $out;
	}
	/**
	 * Getting all the children accounts for the current account
	 * 
	 * @param bool $inclSelf Whether to include it own value
	 * 
	 * @return Ambigous <array(HydraEntity), HydraEntity, multitype:, string, multitype:multitype: >
	 */
	public function getChildren($inclSelf=false)
	{
		$service = new BaseService(get_class($this));
		$where = "accountNumber like '".$this->getAccountNumber()."%'";
		if(!$inclSelf)
			$where .=" AND id != ".$this->getId();
		return $service->findByCriteria($where);
	}
	/**
	 * Getting a snapshot of the current account
	 * 
	 * @return string
	 */
	public function getSnapshot()
	{
		return $this->getRoot() . " - " . $this->getName() . " - $" . $this->getSum();
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
	public function getBreadCrumbs($inclSelf = true, $forId = false, $separator = " / ")
	{
		$return = array();
		$parents = $this->getParents($inclSelf);
		$parents = array_reverse($parents);
		foreach($parents as $p)
		{
			if($forId)
				$return[]  = $p->getId();
			else
				$return[]  = $p->getName();
		}
		return implode($separator,$return);
	}
	/**
	 * Getting all the parent accounts
	 * 
	 * @param bool $inclSelf Whether to include it own value
	 * 
	 * @return multitype:AccountEntry unknown
	 */
	public function getParents($inclSelf=false)
	{
		$parents = array();
		if($inclSelf)
			$parents[] = $this;
		$node = $this;
		while(trim($node->getAccountNumber())!=trim($node->getRoot()->getAccountNumber()))
		{
			$node = $node->getParent();
			$parents[] = $node;
		}
		return $parents;
	}
	/**
	 * (non-PHPdoc)
	 * @see HydraEntity::__toString()
	 */
	public function __toString()
	{
		return $this->getName();
	}
	/**
	 * (non-PHPdoc)
	 * @see HydraEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'etr');
		DaoMap::setStringType('name', 'varchar', 255);
		DaoMap::setIntType("accountNumber", "int", 41);
		DaoMap::setStringType('comments', 'varchar', 255);
		DaoMap::setStringType('value', 'varchar');
		DaoMap::setStringType('budget', 'varchar');
		DaoMap::setManyToOne("parent", "AccountEntry", "petr", null, true);
		DaoMap::setManyToOne("root", "AccountEntry", "petrr");
		DaoMap::commit();
	}
}

?>