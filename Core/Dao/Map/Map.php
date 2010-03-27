<?php

class Map
{
	public static $classMap = array();
	public static $fieldMap = array();
	public static $databaseMap = array();
	
	public static function setField(Entity $entity,BaseDescriptor $descriptor)
	{
		$descriptor->entity = get_class($entity);
		self::$fieldMap[$descriptor->entity][$descriptor->field] = $descriptor;
		self::$databaseMap[$descriptor->entity][$descriptor->dbField] = $descriptor;
	}
	
	public static function setSeachFields(Entity $entity,array $names = array(),$checkNames = false)
	{
		if($checkNames)
		{
			$output = array();
			foreach($names as $name)
			{
				if(!array_key_exists($name,self::$fieldMap[get_class($entity)]))
					$output[] = $name; 
			}
			
			if(sizeof($output) > 0)
				throw new Exception('You are attempting to Search a field(s) that doesnt exist: '.implode(', ',$output));
		}
		
		self::$classMap[get_class($entity)]["search"] = $names;
	}
	
	public static function setEagerFields(Entity $entity,array $names = array(),$checkNames = false)
	{
		if($checkNames)
		{
			$output = array();
			foreach($names as $name)
			{
				$nameParts = explode('.',$name);
				
				for($i=0;$i<sizeof($nameParts);$i+=2)
				{
					if(!Map::isEagerJoin($nameParts[0],$nameParts[1]))
						$output[] = "'".$name."' due to: ".$nameParts[0]."::".$nameParts[1]." are not an EagerLoadable Join";
				}
			}
			
			if(sizeof($output) > 0)
			{
				throw new Exception('You are attempting to eagerload a field(s) that cannot be loaded: '.implode(', ',$output));
			}
		}
		
		self::$classMap[get_class($entity)]["eagerLoad"] = $names;
	}
	
	public static function getSearchFields($class)
	{
		self::lazyLoadMeta($class);
		if(isset(self::$classMap[$class]) && isset(self::$classMap[$class]["search"]))
			return self::$classMap[$class]["search"];
		else
			return array();
	}

	public static function getEagerLoadFields($class)
	{
		self::lazyLoadMeta($class);
		if(isset(self::$classMap[$class]) && isset(self::$classMap[$class]["eagerLoad"]))
			return self::$classMap[$class]["eagerLoad"];
		else
			return array();
	}	
	
	public static function lazyLoadMeta($class)
	{
		if(!self::hasClass($class))
		{
			$temp = new $class();
			$temp->meta();		
		}
	}
	
	public static function getField($class,$field)
	{
		self::lazyLoadMeta($class);
		if(isset(self::$fieldMap[$class][$field]))
			return self::$fieldMap[$class][$field];
		else {			
			throw new Exception("Unable To locate field named: $class::$field.");
		}
			
	}
	
	public static function getFields($class)
	{
		self::lazyLoadMeta($class);
		if(isset(self::$fieldMap[$class]))
			return self::$fieldMap[$class];
		else
			throw new Exception("Unable To locate class named: ".$class);		
	}
	
	public static function getDatabaseFields($class)
	{
		self::lazyLoadMeta($class);
		if(isset(self::$databaseMap[$class]))
			return self::$databaseMap[$class];
		else
			throw new Exception("Unable To locate database class named: ".$class);		
	}
	
	public static function getDatabaseField($class,$field)
	{
		self::lazyLoadMeta($class);
		if(isset(self::$databaseMap[$class][$field]))
			return self::$databaseMap[$class][$field];
		else
			throw new Exception("Unable To locate database field named: $class::$field.");		
	}
	
	public static function isJoin($class,$field)
	{
		self::lazyLoadMeta($class);
		if(isset(self::$fieldMap[$class][$field]))
			return self::$fieldMap[$class][$field] instanceof Join;
		else
			throw new Exception("Unable To locate field named for join check: $class::$field.");		
	}
	
	public static function isEagerJoin($class,$field)
	{
		self::lazyLoadMeta($class);
		if(isset(self::$fieldMap[$class][$field]))
			return self::$fieldMap[$class][$field] instanceof EagerJoin;
		else
			throw new Exception("Unable To locate field named for eager join check: $class::$field.");		
	}
	
	public static function hasClass($class)
	{
		return isset(self::$fieldMap[$class]);
	}

	public static function hasField($class,$field)
	{
		return isset(self::$fieldMap[$class][$field]);
	}
}
?>