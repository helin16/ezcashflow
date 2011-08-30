<?php

class ManyToMany extends Join
{
	public $otherSideField;
	public $side;
	
	const LEFT = true;
	const RIGHT = false;
	
	public function __construct($field,$class,$otherSideField,$side=ManyToMany::LEFT)
	{
		parent::__construct($field,$class);
		$this->otherSideField  = $otherSideField;
		$this->side = $side;
	}
	
	public function generateJoinTableName()
	{
		$side1 = $this->entity.'_'.$this->field;
		$side2 = $this->class.'_'.$this->otherSideField;
		
		if($this->side == ManyToMany::LEFT)
			return $side1.'_'.$side2;
		else
			return $side2.'_'.$side1;
	}	
	
	public function load(Entity $entity)
	{
		if($this->class instanceof Entity)
			$sql = new Select($this->class);
		else
			$sql = new Select(new $this->class());
		$joinTableName = $this->generateJoinTableName();
		$c = new $this->class();
		$alias = $c->getMetaAlias();
//		echo "<pre>";
//		var_dump(debug_backtrace());die;
//		echo "</pre>";
		$sql->addJoin(new SQL_Join('left join',$joinTableName,$joinTableName,$this->field."Id",$this->class,"Id"));
//		var_dump($sql->toSQL());die;
		$dao = new GenericDAO($this->class);
		return $dao->executeStatement($sql);		
	}

	public function __toString()
	{
		return "";
	}		
}

?>