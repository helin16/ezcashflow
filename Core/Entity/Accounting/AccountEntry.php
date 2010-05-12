<?php

class AccountEntry extends HydraEntity
{
	private $name;
	private $accountNumber;
	private $comments;
	private $value;
	
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
		
		DaoMap::setManyToOne("parent","AccountEntry","petr");
		DaoMap::setManyToOne("root","AccountEntry","petrr");
		
		DaoMap::commit();
	}
}

?>