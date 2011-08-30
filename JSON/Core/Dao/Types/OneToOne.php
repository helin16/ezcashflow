<?php

class OneToOne extends OneToMany
{
	public function __construct($field,$class,$disambiguator = null,$isNullable = true)
	{
		parent::__construct($field,$class,$disambiguator,$isNullable);
	}
	
	public function load(Entity $entity)
	{
		$results = parent::load($entity);
		
		if(sizeof($results) > 1)
			throw new Exception("Join returned multiple(".sizeof($results).") results when one was expected.");
			
		return $results[0];
	}
}

?>