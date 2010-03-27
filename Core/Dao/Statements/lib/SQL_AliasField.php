<?php

class SQL_AliasField extends SQL_Field
{
	public $alias;

	public function __construct($field,$alias)
	{
		parent::__construct($field);
		$this->alias = $alias;
	}
	
	public function __toString()
	{
		return ' `'.$this->alias.'`.'.parent::__toString();
	}
}

?>