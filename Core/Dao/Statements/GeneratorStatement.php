<?php

abstract class GeneratorStatement extends BaseStatement
{
	protected function generateSQL()
	{
		return '';
	}
	
	public function toSQL()
	{
		if($this->sql == "")
			$this->sql = $this->generateSQL();	
		
		return $this->sql;
	}
	
	public function setExecutionResult($value)
	{

	}
}

?>