<?php

class SqlStatement extends GeneratorStatement 
{
	protected $doResults = false;
	
	protected $result = null;
	
	/**
	 * getter doResults
	 *
	 * @return doResults
	 */
	public function getDoResults()
	{
		return $this->doResults;
	}
	
	/**
	 * Setter doResults
	 *
	 * @param doResults doResults
	 */
	public function setDoResults($doResults)
	{
		$this->doResults = $doResults;
	}
	
	public function setSQL($sql)
	{
		$this->sql = $sql;
	}

	public function setExecutionResult($value)
	{
		$this->result = $value;
	}
	
	public function getResultSet()
	{
		return $this->result;
	}
}


?>