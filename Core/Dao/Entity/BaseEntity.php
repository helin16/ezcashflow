<?php

abstract class BaseEntity
{
	public function meta()
	{
		if(!Map::hasClass($this->getMetaAlias()))
			$this->__meta();
	}
	
	public function getMetaAlias()
	{
		return get_class($this);
	}
	
	protected function __meta()
	{
		/*var_dump(get_class($this));*/
	}
	
	public function __get($field)
	{
		$className = get_class($this);
		$this->meta();
		$descriptor = Map::getField($className,$field);

		if($descriptor instanceof Join)
		{
			$this->$field = $descriptor->load($this);
			return $this->$field;
		} else {
			$method = 'get'.ucfirst($field);
			if(method_exists($this,$method))
				return $this->$method();
			else
				return $this->$field;
		}
	}
	
	public function __set($field,$value)
	{
		$className = get_class($this);
		$this->meta();
		
		if(Map::hasField($className,$field))
		{
			$descriptor = Map::getField($className,$field);
			
			if($descriptor instanceof EagerJoin && !($value instanceof Entity))
			{
				$field = $descriptor->fieldPrefix.$field;
			}
		}
		
		$this->$field = $value;
	}
}

?>