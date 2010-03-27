<?php

class BaseStatement
{
	protected $calculateTotalRows = false;
	
	protected $sql = "";
	
	/**
	 * getter calculateTotalRows
	 *
	 * @return boolean
	 */
	public function getCalculateTotalRows()
	{
		return $this->calculateTotalRows;
	}
	
	/**
	 * Setter calculateTotalRows
	 *
	 * @param boolean calculateTotalRows
	 */
	public function setCalculateTotalRows($calculateTotalRows)
	{
		$this->calculateTotalRows = $calculateTotalRows;
	}
	
	public function toSQL()
	{
		return $this->sql;
	}
	
}

?>