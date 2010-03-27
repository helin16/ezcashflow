<?php

class TDecimal extends BaseDescriptor
{
	public $descimal;
	
	public function __construct($field,$width = 12,$decimal = 12,$default = 0)
	{
		parent::__construct($field,'Decimal',$width,$default);
		$this->descimal = $decimal;
	}
	
	protected function makeType()
	{
		return $this->type."(".$this->width.", ".$this->descimal.") ";
	}	
}

?>