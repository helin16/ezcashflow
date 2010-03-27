<?php

class ManyToOne extends EagerJoin
{
	public function __construct($field,$class,$nullable=false,$fieldPrefix = "ProxyObject_")
	{
		parent::__construct($field,$class,$nullable,$fieldPrefix);		
	}
		
	public function makeValue($value)
	{
		if($value == null)
			if($this->isNullable)
				return 'null';
			else
				throw new Exception("Non-nullable field ".$this->field." has been attempted to be saved while null");

		return $value->getId();
	}	
	
	public function load(Entity $entity)
	{
		$dao = new GenericDAO($this->class);
		$field = $this->fieldPrefix.$this->field;
				
		if(!isset($entity->$field))
		{
			return null;
		}

		$results = $dao->findById($entity->$field);
		return $results;
	}		
}

?>