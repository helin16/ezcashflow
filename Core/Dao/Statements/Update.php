<?php

class Update extends TypedStatement
{
	protected function generateTableName()
	{
		return $this->type->getMetaAlias();
	}

	protected function generateFieldValuePairs(array $fields)
	{
		$output = "";
		for($i=0;$i<sizeof($fields);$i++)
		{
			if($i!=0)
				$output .= ", ";
			$output .= '`'.$fields[$i]->field.'` = '.$fields[$i]->value; 			
		}
		return $output;	
	}
		
	protected function generateSQL()
	{
		$fields = $this->createFields();
		$sql = "UPDATE `".$this->generateTableName()."` SET ".$this->generateFieldValuePairs($fields)." WHERE `".$this->generateTableName()."`.`id` = ".$this->type->getId()." LIMIT 1;";
		return $sql;
	}
	
	protected function chooseField(BaseDescriptor $descriptor)
	{
		return !($descriptor instanceof OneToMany) && !($descriptor->isAutoIncrement) && !($descriptor instanceof ManyToMany);
	}	
}

?>