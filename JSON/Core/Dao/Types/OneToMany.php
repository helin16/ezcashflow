<?php

class OneToMany extends Join
{
	public $disambiguator;
	
	public function __construct($field,$class,$disambiguator = null,$isNullable = true)
	{
		parent::__construct($field,$class);
		$this->disambiguator = $disambiguator;
		$this->isNullable = $isNullable;
	}

	public function load(Entity $entity)
	{
		$dbField = "";
		$entityAlias = "";

		if($this->disambiguator == null)
			foreach(Map::getFields($this->class) as $field)
			{
				if($field instanceof Join && $field->class == $this->entity)
				{
					$dbField = $field->dbField;
					$d_entity = new $field->entity();
					$entityAlias = $d_entity->getMetaAlias();
					break; 
				}
			}
		else {
			$field = Map::getField($this->class,$this->disambiguator);
			$dbField = $field->dbField;
			$d_entity = new $field->entity();
			$entityAlias = $d_entity->getMetaAlias();
		}

		$dao = new GenericDAO($field->entity);
		$result = $dao->findByCriteria($entityAlias.".".$dbField." = ".$entity->getId());
		
		if(!$this->isNullable && sizeof($result) == 0)
			throw new Exception("Join returned empty set on non-nullable field ");
		
		return $result;
	}
	
	public function __toString()
	{
		return "";
	}	
}

?>