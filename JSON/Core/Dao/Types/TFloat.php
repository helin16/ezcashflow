<?php
class TFloat extends BaseDescriptor 
{
	public function __construct($field,$width = 20,$nullable=false,$default ='0.0')
	{
		parent::__construct($field,'FLOAT',$width,$default,FALSE,$nullable);
	}
	
	protected function makeType()
	{
		return $this->type."(".$this->width.") ";
	}	
	
	public function makeValue($value)
	{
		return "'".(string)$value."'";
	}
}
?>