<?php

class AccountEntry extends ProjectEntity
{
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
	
	protected function __meta()
	{
		parent::__meta();

		Map::setField($this,new TString("name"));
		Map::setField($this,new TInt("accountNumber",41));
		Map::setField($this,new TString("comments",64000));
		Map::setField($this,new TString("value"));
		Map::setField($this,new ManyToOne("parent","AccountEntry",true));
		Map::setField($this,new ManyToOne("root","AccountEntry",true));
	}	
}

?>