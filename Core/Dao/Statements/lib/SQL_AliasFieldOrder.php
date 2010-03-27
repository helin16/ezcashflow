<?php

class SQL_AliasFieldOrder extends SQL_AliasField
{
	public $order;
	
	public function __construct($field,$alias,$order)
	{
		parent::__construct($field,$alias);
		$this->order = $order;
	}
	
	public function __toString()
	{
		return parent::__toString()." ".$this->order;
	}
}


?>