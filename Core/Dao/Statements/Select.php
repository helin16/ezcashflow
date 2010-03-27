<?php

class sClass
{
	public $name;
	public $fields;	
	
	public function __construct($name)
	{
		$this->name = $name;
	}
	
	public function addField(sField $field)
	{
		$this->fields[] = $field;
		return $field;
	}

	public function search($path,$value,$level = 0,$uid = 100)
	{	
		foreach($this->fields as $field)
		{
			if($field->name == $path[$level])
				if($field->search($path,$value,$level+1,$uid))
					return true;				
		}
		
		return false;
	}
}

class sField
{
	public $name;
	public $uid;
	public $value;
	
	public function __construct($name,$value,$uid = 100)
	{
		$this->name = $name;
		$this->value = $value;
		$this->uid = $uid;
	}

	public function search($path,$value,$level,$uid = 100)
	{
		if($this->value instanceof sClass)
			$this->value->search($path,$value,$level+1,$uid);
		else
			$this->value = $value;
		$this->uid = $uid;
		return true;
	}
}

class Select extends GenericSelect
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
	
	protected $map = null;
	
	protected $eagerLoadFields = array();
	protected $searchFields = array();
	protected $searchValue = null;
	
	protected $searchActiveOnly = true;
	
	public $debug = false;
	
	public function __construct(Entity &$type,$pageNumber = null,$pageSize = 30,$searchActiveOnly=true)
	{
		$this->type = &$type;
		$this->typeName = get_class($type);
		$this->eagerLoadFields = Map::getEagerLoadFields($this->typeName);
		$this->searchFields = Map::getSearchFields($this->typeName);
		$this->searchActiveOnly = $searchActiveOnly;
		 
		parent::__construct($this->typeName,$this->type->getMetaAlias(),$pageNumber,$pageSize);
	}
	
	public function search($value = null,array $searchFields = array())
	{
		$this->searchValue = $value;
		
		if(sizeof($searchFields) > 0)
			$this->searchFields = $searchFields;
			
		return $this;
	}
	
	public function orderBy($name,$direction)
	{
		$names = explode('.',$name);
		$size = sizeof($names);
		if($size == 1)
		{
			$dbField = Map::getDatabaseField($this->tableName,$name)->makeFieldName();
			$this->addOrderBy(new SQL_AliasFieldOrder($dbField,$this->tableName,$direction));	
		} else {
			throw new Exception("TODO: !!!!!");
		}
	}
	
	public function ActiveOnly($bool)
	{
		$this->searchActiveOnly = $bool;
	}
	
	protected function generateSearch($preAnd = false)
	{
		$where = array();
		foreach($this->searchFields as $field)
			$where[] = ' `'.$this->type->getMetaAlias()."`.`".$field."` LIKE '".$this->searchValue."'";

		if($preAnd)
			return " AND (".implode(" OR ",$where).")";
		else
			return " (".implode(" OR ",$where).")";
	}
	
	protected function generateWhere()
	{
		$activeSql = "";
		$searchSql = "";
		
		if($this->searchActiveOnly)
			$activeSql = " and `".$this->tableName."`.`active` = 1 ";
		
		if($this->searchValue != null && (sizeof($this->searchFields) > 0))
			$searchSql = " ".$this->generateSearch(true)." ";
		
		if($this->where != "")
			$whereSQL = $this->where;
		else
			$whereSQL = '1 ';	

		return "\nWHERE ".$whereSQL.$activeSql.$searchSql;
	}	
	
	protected function processMap($class,$path,$level = 0,&$tree = null)
	{
		$fields = Map::getFields($class);
		
		if($level == 0)
			$tree = new sClass($class);
		else {
			$tree->value = new sClass($class);
			$tree = $tree->value;
		}		
		
		foreach($fields as $field)
		{
			if($field instanceof EagerJoin)
			{
				if($path == "")
				{
					$tableAlias = $class;
					$alias = $class.".".$field->field;					
				} else {
					$tableAlias = str_replace('.','_',$path);
					$alias = $path.".".$class.".".$field->field;					
				}				
				
				$this->addField(new SQL_AliasFieldName($field->dbField,$tableAlias,$alias));
				$node = $tree->addField(new sField($field->field,null));

				if(array_search($alias,$this->eagerLoadFields) !== false)
				{
					$this->addJoin(new SQL_Join(SQL::JOIN_LEFT,$field->class,str_replace('.','_',$alias),'id',$tableAlias,$field->dbField));
					$this->processMap($field->class,$alias,$level + 1,$node);
				}
				continue;			
			} else if(!($field instanceof Join))
			{	
				if($path == "")
				{
					$tableAlias = $class;
					$alias = $class.".".$field->field;
				} else {
					$tableAlias = str_replace('.','_',$path);
					$alias = $path.".".$class.".".$field->field;
				}
				
				$tree->addField(new sField($field->field,null));
				$this->addField(new SQL_AliasFieldName($field->dbField,$tableAlias,$alias));
			} else {
				continue;
			}
		}
	}
	
	protected function generateSQL()
	{
		$this->processMap($this->tableName,'',0,$this->map);
		return parent::generateSQL();
	}
	
	protected function populate(&$object, $node,$uid = 100)
	{
		foreach($node->fields as $child)
		{
			$field = $child->name;
			
			if($child->uid != $uid)
			{
				continue;
			}
			
			if($child->value instanceof sClass)
			{
				$id = null;
				foreach($child->value->fields as $pos)
				{
					if($pos->name == "id")
					{
						$id = $pos->value;
						break;
					}
				}

				if($id != null)
					$object->$field = $this->objectify($child->value,$uid);
				else
					$object->$field = null;
			} else
				$object->$field = $child->value;
		}
	}
	
	protected function objectify($node,$uid = 100)
	{
		$object = new $node->name();
		$this->populate($object,$node,$uid);
		return $object;
	}
	
	public function setExecutionResult($value)
	{
		$this->resultSet = array();
		
		if($this->debug)
			var_dump($value);
		
		$i = 101;
		foreach($value as $row)
		{
			foreach($row as $key => $value)
			{
				$path = explode('.',$key);
				$this->map->search($path,$value,1,$i);
			}
			$this->resultSet[] = $this->objectify($this->map,$i);
			
			$i++;
		}
		
		if($this->debug)
			var_dump($this->resultSet);
	}	
}

?>