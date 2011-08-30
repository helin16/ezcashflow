<?php

class TDate extends BaseDescriptor
{
	private $updateOnCurrentTimeStamp=false;
	public function __construct($field,$updateOnCurrentTimeStamp=false)
	{
		parent::__construct($field,($updateOnCurrentTimeStamp==false?'DATETIME':'TIMESTAMP'),0,0);
		$this->updateOnCurrentTimeStamp = $updateOnCurrentTimeStamp;
	}
	
	protected function makeType()
	{
		return $this->type.' ';
	}	

	public function makeValue($value)
	{
		return "'".(string)$value."'";
	}
	
	public function __toString()
	{
		$string =  $this->makeField().$this->makeType().$this->makeNull();
		if($this->updateOnCurrentTimeStamp==true)
		{
			$string .= " default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP";
		}
		return $string;
	}		
}

?>