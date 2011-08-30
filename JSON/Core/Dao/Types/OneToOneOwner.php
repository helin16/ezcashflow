<?php

class OneToOneOwner extends ManyToOne
{
	public function __construct($field,$class,$nullable=false,$fieldPrefix = "ProxyObject_")
	{
		parent::__construct($field,$class,$nullable,$fieldPrefix);		
	}
	
	public function load(Entity $entity)
	{
		$results = parent::load($entity);
		
		if(sizeof($results) > 1)
			throw new Exception("OneToOneOwner Join returned multiple(".sizeof($results)." results.");
		
		return $results;
	}			
}

?>