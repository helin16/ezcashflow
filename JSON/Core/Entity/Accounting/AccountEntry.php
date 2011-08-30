<?php

class AccountEntry extends HydraEntity
{
	private $name;
	private $accountNumber;
	private $comments;
	private $value;
	private $budget;
	
	protected $root;
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
	 * @var name
	 */
	public function setName($name)
	{
		$this->name = $name;
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
	 * @var accountNumber
	 */
	public function setAccountNumber($accountNumber)
	{
		$this->accountNumber = $accountNumber;
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
	 * @var comments
	 */
	public function setComments($comments)
	{
		$this->comments = $comments;
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
	 * @var value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	/**
	 * getter parent
	 *
	 * @return parent
	 */
	public function getParent()
	{
		$this->loadManyToOne("parent");
		return $this->parent;
	}
	
	/**
	 * setter parent
	 *
	 * @var parent
	 */
	public function setParent($parent)
	{
		$this->parent = $parent;
	}
	
	/**
	 * getter root
	 *
	 * @return root
	 */
	public function getRoot()
	{
		$this->loadManyToOne("root");
		return $this->root;
	}
	
	/**
	 * setter root
	 *
	 * @var root
	 */
	public function setRoot($root)
	{
		$this->root = $root;
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
	 * @var budget
	 */
	public function setBudget($budget)
	{
		$this->budget = $budget;
	}
	
	public function getSum($includeChildren=false,$inclSelf=true)
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
	
	public function getChildren($inclSelf=false)
	{
		$service = new BaseService(get_class($this));
		$where = "accountNumber like '".$this->getAccountNumber()."%'";
		if(!$inclSelf)
			$where .=" AND id != ".$this->getId();
		return $service->findByCriteria($where);
	}
	
	public function getSnapshot()
	{
		return $this->getRoot()." - ".$this->getName()." - $".$this->getSum();
	}
	
	public function getLongshot()
	{
		return $this->getBreadCrumbs()." - $".$this->getSum();
	}
	
	public function getBreadCrumbs($inclSelf=true,$forId=false,$separator=" / ")
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
	
	public function __toString()
	{
		return $this->getName();
	}
	
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'etr');
		
		DaoMap::setStringType('name','varchar',255);
		DaoMap::setIntType("accountNumber","int",41);
		DaoMap::setStringType('comments','varchar',255);
		DaoMap::setStringType('value','varchar');
		DaoMap::setStringType('budget','varchar');
		
		DaoMap::setManyToOne("parent","AccountEntry","petr",null,true);
		DaoMap::setManyToOne("root","AccountEntry","petrr");
		
		DaoMap::commit();
	}
}

?>