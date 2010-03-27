<?php

class Insert extends TypedStatement
{
	protected function generateTableName()
	{
		return $this->type->getMetaAlias();
	}
	
	protected function generateFieldList(array $fields)
	{
		$output = "";
		for($i=0;$i<sizeof($fields);$i++)
		{
			if($i!=0)
				$output .= ", ";
			$output .= '`'.$fields[$i]->field.'`'; 			
		}
		return $output;
	}
	
	protected function generateValueList(array $fields)
	{
		$output = "";
		for($i=0;$i<sizeof($fields);$i++)
		{
			if($i!=0)
				$output .= ", ";
			$output .= $fields[$i]->value; 			
		}
		return $output;
	}
		
	protected function generateSQL()
	{
		$fields = $this->createFields(); 
		$sql = "INSERT INTO `".$this->generateTableName()."` (".$this->generateFieldList($fields).") VALUES (".$this->generateValueList($fields).");";
		return $sql;		
	}
	
	protected function chooseField(BaseDescriptor $descriptor)
	{
		return !($descriptor instanceof OneToMany) && !($descriptor->isAutoIncrement) && !($descriptor instanceof ManyToMany);
	}
	
	public function setExecutionResult($value)
	{
		$this->type->setId($value);
	}
}

?>