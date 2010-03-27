<?php

class SQL_Join
{
	public $joinType;
	public $table;
	public $tableAlias;
	public $tableField;
	public $joinAlias;
	public $joinField;
	public $where = "";
		
	public function __construct($joinType,$table,$tableAlias,$tableField,$joinAlias,$joinField,$where = "")
	{
		$this->joinType = $joinType;
		$this->table = $table;
		$this->tableAlias = $tableAlias;
		$this->tableField = $tableField;
		$this->joinAlias = $joinAlias;
		$this->joinField = $joinField;
		if($where != "")
			$this->where = ' and '.$where;
		
	}
	
	public function __toString()
	{
		return $this->joinType." `".$this->table."` ".$this->tableAlias." ON (".$this->tableAlias.".".$this->tableField." = ".$this->joinAlias.".".$this->joinField.' '.$this->where.')';
	}
}


?>