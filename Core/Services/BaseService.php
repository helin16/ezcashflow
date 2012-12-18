<?php
/**
 * Basic Abstract entity service
 * 
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 */
abstract class BaseService
{
	/**
	 * @var GenericDao
	 */
	protected $entityDao;
	/**
	 * Total Number Of Rows, after a search query has run
	 *
	 * @var int
	 */
	public $totalNoOfRows;
	/**
	 * constructor
	 * 
	 * @param string $entityName The entity name of the service
	 */
	public function __construct($entityName)
	{
		$this->entityDao = new GenericDao($entityName);
		$this->totalNoOfRows = 0;
	}
	/**
	 * Get an Entity By its Id
	 *
	 * @param int $id The id of the entity
	 * 
	 * @return HydraEntity
	 */
	public function get($id)
	{
		return $this->entityDao->findById($id);
	}
	/**
	 * Save an Entity
	 *
	 * @param Entity $entity The entity we are trying to save
	 * 
	 * @return HydraEntity
	 */
	public function save(HydraEntity $entity)
	{
	    $this->entityDao->save($entity);
	    return $entity;
	}
	/**
	 * Finding all entries for that entity
	 * 
	 * @param bool  $searchActiveOnly Whether we will get the active one only
	 * @param int   $page             The page number of the pagination
	 * @param int   $pagesize         The page size of the pagination
	 * @param array $orderBy          The order by fields. i.e.: array("UserAccount.id" => 'desc');
	 * 
	 * @return Ambigous <array(HydraEntity), multitype:, string, multitype:multitype: >
	 */
	public function findAll($searchActiveOnly = true,$page = null,$pagesize = 30,$orderBy=array())
	{
		if ($searchActiveOnly == false)
			Dao::$AutoActiveEnabled = false;
		$temp = $this->entityDao->findAll($page, $pagesize);
		$this->totalNoOfRows = $this->entityDao->getTotalRows();
		if ($searchActiveOnly == false)
			Dao::$AutoActiveEnabled = true;
		return $temp;
	}
	/**
	 * Finding some entries for that entity
	 * 
	 * @param string $where            The where clause for the sql
	 * @param bool   $searchActiveOnly Whether we will get the active one only
	 * @param int    $page             The page number of the pagination
	 * @param int    $pagesize         The page size of the pagination
	 * @param array  $orderBy          The order by fields. i.e.: array("UserAccount.id" => 'desc');
	 * 
	 * @return Ambigous <array(HydraEntity), HydraEntity, multitype:, string, multitype:multitype: >
	 */
	public function findByCriteria($where, $searchActiveOnly=true, $page = null, $pagesize = 30, $orderBy=array())
	{
		if ($searchActiveOnly == false)
			Dao::$AutoActiveEnabled = false;
		$temp =  $this->entityDao->findByCriteria($where, array(), $page, $pagesize, $orderBy);
		$this->totalNoOfRows = $this->entityDao->getTotalRows();
		if ($searchActiveOnly == false)
			Dao::$AutoActiveEnabled = true;
		return $temp;
	}
}
?>