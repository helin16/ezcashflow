<?php
class BaseService
{
	/**
	 * @var GenericDao
	 */
	protected $entityDao;
	/**
	 * Total Number Of Rows, after a search query has run
	 *
	 * @var unknown_type
	 */
	public $totalNoOfRows;
	
	public function __construct($entityName)
	{
		$this->entityDao = new GenericDao($entityName);
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
	public function save(HydraEntity $entity)
	{
		$this->entityDao->save($entity);
	}
	
	public function findAll($searchActiveOnly=true,$page = null,$pagesize = 30,$orderBy=array())
	{
		if ($searchActiveOnly == false)
			Dao::$AutoActiveEnabled = false;
		
		$temp = $this->entityDao->findAll($page, $pagesize);
		$this->totalNoOfRows = $this->entityDao->getTotalRows();
		if ($searchActiveOnly == false)
			Dao::$AutoActiveEnabled = true;
		
		return $temp;
	}
	
	public function findByCriteria($where,$searchActiveOnly=true,$page = null,$pagesize = 30,$orderBy=array())
	{
		if ($searchActiveOnly == false)
			Dao::$AutoActiveEnabled = false;
			
		$temp =  $this->entityDao->findByCriteria($where,array(),$page,$pagesize,$orderBy);
		$this->totalNoOfRows = $this->entityDao->getTotalRows();
		
		if ($searchActiveOnly == false)
			Dao::$AutoActiveEnabled = true;
		
		return $temp;
	}
}
?>