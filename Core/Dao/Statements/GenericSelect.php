<?php

class GenericSelect extends GeneratorStatement
{
	protected $fields = array();
	protected $joins = array();
	protected $tableName = "";
	protected $tableAlias = "";
	protected $where = "";
	protected $orderBy = array();
	protected $groupBy = array();
	protected $resultSet = array();
	protected $page = null;
	protected $pageSize = 30;
	protected $totalRows = "";
	protected $distinct = true;
	
	public function __construct($tableName,$tableAlias,$page = null,$pageSize = 30)
	{
		$this->setCalculateTotalRows(true);
		$this->tableName = $tableName;
		$this->tableAlias = $tableAlias;
		$this->page = $page;
		$this->pageSize = $pageSize;
	}
	
	public function addField(SQL_AliasFieldName $field)
	{
		$this->fields[] = $field;
	}
	
	public function addJoin(SQL_Join $join)
	{
		$this->joins[] = $join;
	}
	
	public function addOrderBy(SQL_AliasFieldOrder $field)
	{
		$this->orderBy[] = $field;
	}
	
	public function addGroupBy(SQL_AliasField $field)
	{
		$this->groupBy[] = $field;
	}
	
	/**
	 * getter where
	 *
	 * @return String
	 */
	public function getWhere()
	{
		return $this->where;
	}
	
	/**
	 * Setter where
	 *
	 * @param String where
	 */
	public function setWhere($where)
	{
		$this->where = $where;
	}
	
	/**
	 * getter distinct
	 *
	 * @return Boolean
	 */
	public function getDistinct()
	{
		return $this->distinct;
	}
	
	/**
	 * Setter distinct
	 *
	 * @param Boolean distinct
	 */
	public function setDistinct($distinct)
	{
		$this->distinct = $distinct;
	}
	
	protected function generateFieldList()
	{
		return implode(",\n\t",$this->fields);
	}
	
	protected function generateTableName()
	{
		return '`'.$this->tableName."` `".$this->tableAlias."` ";
	}
	
	protected function generateJoins()
	{
		if(sizeof($this->joins) > 0)
		 	return implode(" \n",$this->joins)." ";
		 else
		 	return "";
	}
	
	protected function generateWhere()
	{
		if($this->where != "")
			return "\nWHERE ".$this->where." ";
		else
			return "";
	}
	
	protected function generateGroupBy()
	{
		if(sizeof($this->groupBy) > 0)
			return "GROUP BY ".implode(", ",$this->groupBy)." ";
		else
			return "";
	}
	
	protected function generateOrderBy()
	{
		if(sizeof($this->orderBy) > 0)
			return "ORDER BY ".implode(", ",$this->orderBy)." ";
		else
			return "";
		
	}
	
	protected function generateLimit()
	{
		if($this->page !== null)
			return "LIMIT ".($this->page * $this->pageSize).", ".$this->pageSize;
		else
			return "";
	}
	
	protected function generateSQL()
	{
		$distinctSQL = "";
		if($this->distinct)
			$distinctSQL = "DISTINCT";
		
		$sql = "SELECT ".$distinctSQL." SQL_CALC_FOUND_ROWS ".$this->generateFieldList()." \nFROM ".$this->generateTableName()." \n".$this->generateJoins().$this->generateWhere().$this->generateGroupBy().$this->generateOrderBy().$this->generateLimit().";";	
		return $sql;
	}
	
	public function setExecutionResult($value)
	{
		$this->resultSet = $value;
	}
	
	public function getResultSet()
	{		
		return $this->resultSet;
	}
}

?>