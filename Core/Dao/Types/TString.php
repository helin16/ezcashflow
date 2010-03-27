<?php

class TString extends BaseDescriptor
{
	public function __construct($field,$width = 30,$default='',$type='varchar')
	{
		parent::__construct($field,$type,$width,$default);
	}	

	public function makeValue($value)
	{
		return "'".(string)$value."'";
	}
}

?>