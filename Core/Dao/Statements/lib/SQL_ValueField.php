<?php

class SQL_ValueField
{
	public $field;
	public $value;

	public function __construct($field,$value)
	{
		$this->field = $field;
		$this->value = $value;
	}
	
	public function __toString()
	{
		return $this->field;
	}
}

?>