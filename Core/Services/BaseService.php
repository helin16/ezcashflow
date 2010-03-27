<?php
class BaseService
{
	/**
	 * @var GenericDao
	 */
	protected $entityDao;
	/**
	 * @var String
	 */
	protected $entityAlias="";
	/**
	 * Total Number Of Rows, after a search query has run
	 *
	 * @var unknown_type
	 */
	public $totalNoOfRows;
	
	public function __construct($entityName)
	{
		$this->entityDao = new GenericDao($entityName);
		$this->entityAlias = $this->entityDao->getEntity()->getMetaAlias();
		$this->totalNoOfRows = 0;
	}
	
	/**
	 * Get an Entity By its Id
	 *
	 * @param unknown_type $id
	 */
	public function get($id)
	{
		$return = $this->entityDao->findById($id);
		return $return;
	}
	
	/**
	 * Save an Entity
	 *
	 * @param Entity $entity
	 */
	public function save(Entity $entity)
	{
		$this->entityDao->save($entity);
	}
	
	public function findAll($searchActiveOnly=true,$page = null,$pagesize = 30)
	{
		$temp =  $this->entityDao->findAll($page,$pagesize,$searchActiveOnly);
		$this->totalNoOfRows = $this->entityDao->getTotalRows();
		return $temp;
	}
	
	public function findByCriteria($where,$searchActiveOnly=true,$page = null,$pagesize = 30)
	{
		$temp =  $this->entityDao->findByCriteria($where,$page,$pagesize,$searchActiveOnly);
		$this->totalNoOfRows = $this->entityDao->getTotalRows();
		return $temp;
	}
}
?>