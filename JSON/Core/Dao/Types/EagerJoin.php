<?php

abstract class EagerJoin extends Join
{
	public $fieldPrefix;
	
	public function __construct($field,$class,$isNullable=false,$fieldPrefix = "proxyObject_")
	{
		parent::__construct($field,$class);
		$this->isNullable = $isNullable;
		$this->fieldPrefix = $fieldPrefix;
	}

}

?>