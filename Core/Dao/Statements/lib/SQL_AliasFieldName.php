<?php

class SQL_AliasFieldName extends SQL_AliasField
{
	public $name;

	public function __construct($field,$alias,$name)
	{
		parent::__construct($field,$alias);
		$this->name = $name;
	}
	
	public function __toString()
	{
		return parent::__toString()." '".$this->name."'";
	}
}

?>