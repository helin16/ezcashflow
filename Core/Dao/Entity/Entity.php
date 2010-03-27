<?php

abstract class Entity extends BaseEntity
{	
	protected $active = 1;
	
	/**
	 * getter id
	 *
	 * @return id
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Setter id
	 *
	 * @param int id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}
	
	/**
	 * getter active
	 *
	 * @return int
	 */
	public function getActive()
	{
		return $this->active;
	}
	
	/**
	 * Setter active
	 *
	 * @param int active
	 */
	public function setActive($active)
	{
		$this->active = $active;
	}
	
	/**
	 * getter created
	 *
	 * @return UniversalDate
	 */
	public function getCreated()
	{
		if($this->created == "")
			return new UniversalDate();
		else
			return $this->created;
	}
	
	/**
	 * Setter created
	 *
	 * @param created created
	 */
	public function setCreated($created)
	{
		if(is_string($created))
			$this->created  = new UniversalDate($created);
		else
			$this->created = $created;
	}
	
	/**
	 * getter updated
	 *
	 * @return UniversalDate
	 */
	public function getUpdated()
	{
		if($this->updated == "")
			return new UniversalDate();
		else
			return $this->updated;
	}
	
	/**
	 * Setter updated
	 *
	 * @param updated updated
	 */
	public function setUpdated($updated)
	{
		if(is_string($updated))
			$this->updated  = new UniversalDate($updated);
		else
			$this->updated = $updated;
	}
	
	protected function __meta()
	{
		$i = new TInt('id');
		$i->isPrimary = true;
		$i->isAutoIncrement = true;
		Map::setField($this,$i);	
		
		Map::setField($this,new TInt('active',1,1));
		
		Map::setField($this,new TDate('created'));
		Map::setField($this,new TDate('updated',true));
	}	
	
}


?>