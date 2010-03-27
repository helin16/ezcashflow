<?php

class GenericDao
{
	protected static $dao = null;
	protected $entityName = "";
	protected $entity = null;
	
	protected $totalRows = 0;
	
	public function __construct($entity)
	{
		$this->setEntity($entity);
	}
	
	/**
	 * getter entity
	 *
	 * @return String
	 */
	public function getEntity()
	{
		return $this->entity;
	}
	
	/**
	 * Setter entity
	 *
	 * @param String entity
	 */
	public function setEntity($entity)
	{
		if($this->entity instanceof Entity)
		{
			$this->entity = $entity;
			$this->entityName = get_class($entity);
		} else {
			$this->entity = new $entity();
			$this->entityName = $entity;
		}
	}

	/**
	 * Get The shared Dao
	 *
	 * @return Dao
	 */
	private function getDao()
	{
		if(self::$dao == null)
		{
			self::$dao = new Dao();
		}
		return self::$dao;
	}

	protected function setTotalRows()
	{
		$this->totalRows = $this->getDao()->getTotalRows();
	}
	
	public function save(Entity $entity)
	{
		if($entity->getId() == null)
		{
			$sql = new Insert($entity);
			$this->getDao()->execute($sql);
			return $entity;
		} else {
			$sql = new Update($entity);
			$this->getDao()->execute($sql);
			return $entity;			
		}
	}
	
	public function findAll($page = null,$pagesize = 30,$searchActiveOnly=true)
	{
		$sql = new Select($this->entity,$page,$pagesize,$searchActiveOnly);
		$this->getDao()->execute($sql);
		$this->setTotalRows();
		return $sql->getResultSet();
	}
	
	protected function isInteger($input)
	{
    	return(ctype_digit(strval($input)));
	}
	
	public function findById($id)
	{
		
		if(!$this->isInteger($id))
			throw new Exception("Trying to find id of non-int");
		
		$sql = new Select($this->entity);
		$sql->setWhere($this->entity->getMetaAlias().".id = $id");
		$this->getDao()->execute($sql);
		
		$results = $sql->getResultSet();
		$size = sizeof($results);
		
		if($size == 0)
			return null;
		else if($size == 1)
			return $results[0];
		else
			throw new Exception("FindById: Found multiple($size) enties for ".get_class($this->entity)."@$id");
	}
	
	public function findByCriteria($where,$page = null,$pagesize = 30,$searchActiveOnly=true)
	{
		$sql = new Select($this->entity,$page,$pagesize,$searchActiveOnly);
		$sql->setWhere($where);
		$this->getDao()->execute($sql);
		$this->setTotalRows();
		return $sql->getResultSet();		
	}
	
	public function search($target,$page = null,$pagesize = 30,$searchActiveOnly=true)
	{
		$sql = new Select($this->entity,$page,$pagesize,$searchActiveOnly);
		$sql->search($target);
		$this->getDao()->execute($sql);
		$this->setTotalRows();
		return $sql->getResultSet();
	}
	
	public function activate(Entity $entity)
	{
		$entity->setActive(1);
		$this->save($entity);
	}
	
	public function deactivate(Entity $entity)
	{
		$entity->setActive(0);
		$this->save($entity);		
	}
	
	public function executeStatement(BaseStatement $sql)
	{
		$this->getDao()->execute($sql);
		$this->setTotalRows();
		return $sql->getResultSet();		
	}

	/**
	 * getter TotalRows
	 *
	 * @return int
	 */
	public function getTotalRows()
	{
		return $this->totalRows;
	}
}

?>