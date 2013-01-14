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
	 * @var EntityDao
	 */
	protected $entityDao;
	/**
	 * The pagination stats
	 *
	 * @var array
	 */
	private $_pageStats = array();
	/**
	 * constructor
	 * 
	 * @param string $entityName The entity name of the service
	 */
	public function __construct($entityName)
	{
		$this->entityDao = new EntityDao($entityName);
		$this->_pageStats = Dao::getPageStats();
	}
	/**
	 * Get an Entity By its Id
	 *
	 * @param int $id The id of the entity
	 * 
	 * @return BaseEntity
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
	 * @return BaseEntity
	 */
	public function save(BaseEntityAbstract $entity)
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
	 * @return Ambigous <array(BaseEntity), multitype:, string, multitype:multitype: >
	 */
	public function findAll($searchActiveOnly = true, $page = null, $pagesize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array())
	{
		$temp = $this->entityDao->findAll($page, $pagesize, $orderBy);
		$this->_pageStats = Dao::getPageStats();
		return $temp;
	}
	/**
	 * Finding some entries for that entity
	 * 
	 * @param string $where            The where clause for the sql
	 * @param array  $params           The parameters for PDO exec
	 * @param bool   $searchActiveOnly Whether we will get the active one only
	 * @param int    $page             The page number of the pagination
	 * @param int    $pagesize         The page size of the pagination
	 * @param array  $orderBy          The order by fields. i.e.: array("id" => 'desc');
	 * 
	 * @return Ambigous <array(BaseEntity), BaseEntity, multitype:, string, multitype:multitype: >
	 */
	public function findByCriteria($where, $params = array(), $searchActiveOnly = true, $page = null, $pagesize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array())
	{
		$temp = $this->entityDao->findByCriteria($where, $params, $page, $pagesize, $orderBy);
		$this->_pageStats = Dao::getPageStats();
		return $temp;
	}
	/**
	 * returning the pagination stats
	 *
	 * @return array
	 */
	public function getPageStats()
	{
	    return $this->_pageStats;
	}
}
?>