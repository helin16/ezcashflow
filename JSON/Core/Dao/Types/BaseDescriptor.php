<?php

abstract class BaseDescriptor
{
	public $entity;
	public $field;
	public $dbField;
	public $type;
	public $width;
	public $default;
	public $isPrimary;
	public $isNullable;
	public $isAutoIncrement;
	public $isUnique;
	
	
	public function __construct($field,$type,$width,$default=null,$isPrimary=false,$isNullable=false,$isAutoIncrement=false,$isUnique=false)
	{
		$this->field = $field;
		$this->dbField = $this->makeFieldName($this->field);
		$this->type = $type;
		$this->width = $width;
		$this->default = $default;
		$this->isPrimary = $isPrimary;
		$this->isNullable = $isNullable;
		$this->isUnique = $isUnique;
		$this->isAutoIncrement = $isAutoIncrement;
	}
	
	public function makeFieldName()
	{
		return $this->field;
	}
	
	protected function makeField()
	{
		return "`".$this->makeFieldName()."` ";
	}
	
	protected function makeType()
	{
		return $this->type."(".$this->width.") ";
	}
	
	protected function makeNull()
	{
		if($this->isNullable)
			return "NULL ";
		else
			return "NOT NULL ";		
	}
	
	protected function makeAutoIncrement()
	{
		if($this->isAutoIncrement)
			return "auto_increment ";
	}

	public function makeValue($value)
	{
		return (string)$value;
	}
	
	protected function makeDefault()
	{
		if($this->isNullable && $this->default == null)
			return "default NULL ";
		else if($this->default == null)
			return "";
		else
			return "default ".$this->makeValue($this->default)." ";	
	}
	
	public function __toString()
	{
		return $this->makeField().$this->makeType().$this->makeNull().$this->makeDefault().$this->makeAutoIncrement();
	}	
}

?>