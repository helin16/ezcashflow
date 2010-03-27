<?php

class SQL_Field
{
	public $field;

	public function __construct($field)
	{
		$this->field = $field;
	}
	
	public function __toString()
	{
		return $this->field;
	}
}

?>