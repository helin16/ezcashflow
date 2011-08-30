<?php

abstract class Join extends BaseDescriptor
{
	public $class;
	
	public function __construct($field,$class)
	{
		parent::__construct($field,'int',8,0);
		$this->class = $class;
	}
	
	public function load(Entity $entity)
	{
		return null;
	}

	public function makeFieldName()
	{
		return $this->field."Id";
	}	
}

?>