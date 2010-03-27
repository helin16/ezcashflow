<?php

abstract class TypedStatement extends GeneratorStatement
{
	/**
	 * Entity Type;
	 *
	 * @var Entity
	 */
	protected $type; 
	
	/**
	 * Class Name
	 *
	 * @var String
	 */
	protected $typeName;
	
	public function __construct(Entity &$type)
	{
		$this->type = &$type;
		$this->typeName = get_class($type);
	}
	
	protected function chooseField(BaseDescriptor $descriptor)
	{
		return true;
	}
	
	protected function createFields()
	{
		$sqlFields = array();
		$fields = Map::getFields($this->typeName);
		foreach($fields as $field)
		{
			if($this->chooseField($field))
			{
				$getter = $field->field;
				$sqlFields[] = new SQL_ValueField($field->dbField,$field->makeValue($this->type->$getter));
			}
		}
		return $sqlFields;
	}		
}

?>