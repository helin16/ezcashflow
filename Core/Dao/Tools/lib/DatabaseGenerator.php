<?php

class DatabaseGenerator extends FileScanner
{
	private $datebase;
	private $engine;
	private $charset;
	private $collate;
	
	public function __construct($path,$database,$engine='InnoDB',$charset='latin1',$collate="")
	{
		$this->datebase = $database;
		$this->engine = $engine;
		$this->charset = $charset;
		$this->collate = $collate;
		
		parent::__construct($path);
	}
		
	private function generateDropSql($class)
	{
		return "DROP TABLE IF EXISTS `".$this->datebase."`.`$class`;\n";
	}
	
	public function generateDrop()
	{
		$output = array();
		$map = Map::$fieldMap;
		foreach($map as $class => $fields)
		{
			$output[] = $this->generateDropSql($class);
			foreach($fields as $field)
				if($field instanceof ManyToMany && $field->side == ManyToMany::LEFT)
				{
					$output[] = $this->generateDropSql($field->generateJoinTableName());
				}
					 
		}
		return $output;
	}
	
	private function generateCreateSQL($class,$fields)
	{
		$keys = array();
		$joins = array();
		$textFields = array();
		$output = "CREATE TABLE IF NOT EXISTS `".$this->datebase."`.`$class` (\n";
		
		$first = true;
		foreach($fields as $field)
		{			
			if($field instanceof ManyToMany && $field->side == ManyToMany::LEFT)
			{			
				$joins = array_merge($joins,$this->generateCreateSQL($field->generateJoinTableName(),array(new TInt($field->field.'Id'),new TInt($field->otherSideField.'Id'))));
			}

			if($field->isPrimary)
				$keys[] = "\tPRIMARY KEY  (`".$field->field."`)";
			
			if($field->isUnique)
				$keys[] = "\tUNIQUE KEY `".$field->field."` (`".$field->field."`)";
				
			$text = (string)$field;
			if($text != "")
				$textFields[] = "\t".$text;
		}
		
		if(sizeof($keys) > 0)
  			$output .= implode(",\n",$keys).",\n";
  		$output .= implode(",\n",$textFields);
  		$output .= "\n) ENGINE=".$this->engine." DEFAULT CHARSET=".$this->charset;
  		if($this->collate!="")
  			$output .=" COLLATE=".$this->collate;
  		$output .=" AUTO_INCREMENT=1 ;\n";
  		return  array_merge(array($output),$joins);
	}
	
	public function generateCreate()
	{
		$output = array();
		$map = Map::$fieldMap;
		foreach($map as $class => $fields)
			$output = array_merge($output,$this->generateCreateSQL($class,$fields));				 
		return $output;		
	}
}

?>