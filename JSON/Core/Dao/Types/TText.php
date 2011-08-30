<?php

class TText extends BaseDescriptor
{
	public function __construct($field,$width = 8,$default = 0)
	{
		parent::__construct($field,'TEXT',$width,$default);
	}
	
	protected function makeType()
	{
		return $this->type." ";
	}

	public function makeValue($value)
	{
		return "'".(string)$value."'";
	}	
}

?>