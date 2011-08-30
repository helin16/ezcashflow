<?php

/**
 * Class for executing DaoQuery objects and managing database connections
 *
 * @package Core
 * @subpackage Dao
 */
class Dao
{
	const AS_OBJECTS = 1;
	const AS_ARRAY = 2;
	const AS_XML = 3;
	
	private static $dsn = '';
	private static $user = '';
	private static $pass = '';
	
	private static $totalPages = 0;
	private static $totalRows = 0;
	private static $pageSize = 30;
	private static $pageNumber = 1;
	
	public static $Server = 'localhost';
	public static $Debug = false;
	public static $OutputFormat = Dao::AS_OBJECTS;
	public static $LazyLoadingEnabled = true;
	public static $LazyLoadInProgress = false;
	public static $ProfileInProgress = false;
	public static $AutoActiveEnabled = true;
	
	/**
	 * @var DaoFilterSet
	 */
	private static $filterSet = null;
	
	/**
	 * @var PDO
	 */
	private static $db = null;
	
	public static $lastInsertId=null;

	/**
	 * Sets the global filters to apply to each query against the Dao
	 *
	 * @param DaoFilterSet $filters
	 */
	public static function setFilterSet(DaoFilterSet $filterSet)
	{
		self::$filterSet = $filterSet;
	}
	
	/**
	 * Global filters to apply to each query against the Dao
	 *
	 * @return DaoFilterSet
	 */
	public static function getFilterSet()
	{
		return self::$filterSet;
	}
	
	/**
	 * Get page size of the last paged query
	 *
	 * @return int
	 */
	public static function getPageSize()
	{
		return self::$pageSize;
	}

	/**
	 * Get total rows returned if the last query was NOT paged
	 *
	 * @return int
	 */
	public static function getTotalRows()
	{
		return self::$totalRows;
	}

	/**
	 * Get total pages returned if the last query was NOT paged
	 *
	 * @return int
	 */
	public static function getTotalPages()
	{
		return self::$totalPages;
	}

	/**
	 * Get page number of the last paged query
	 *
	 * @return int
	 */
	public static function getPageNumber()
	{
		return self::$pageNumber;
	}
	
	/**
	 * Set an object property via its setter or public property
	 *
	 * @param HydraEntity $entity
	 * @param string $field
	 * @param mixed $value
	 */
	public static function setProperty(HydraEntity &$entity, $field, &$value)
	{
		$method = 'set' . ucwords($field);
		
		if (method_exists($entity, $method))
		{
			$entity->$method($value);
			return;
		}
		
		$property = strtolower(substr($field, 0, 1)) . substr($field, 1);
		$entity->$property = &$value;
		return;
	}

	/**
	 * Get an object property via its getter or public property
	 *
	 * @param HydraEntity $entity
	 * @param string $field
	 * @return mixed
	 */
	public static function getProperty(HydraEntity &$entity, $field)
	{
		$method = 'get' . ucwords($field);
		
		if (method_exists($entity, $method))
		{
			return $entity->$method();
		}
		
		$property = strtolower(substr($field, 0, 1)) . substr($field, 1);
		return $entity->$property;
	}
	
	/**
	 * Connect to the database
	 */
	public static function connect()
	{
		// Only connect if we don't have a handle on the database
		if (!is_null(self::$db))
		{
			return;
		}
		
		try
		{
			// DSN FORMAT: "mysql:host=localhost;dbname=test"
			$driver = Config::get('Database', 'Driver');
			$host = Config::get('Database', 'LoadBalancer');
			$schema = Config::get('Database', 'CoreDatabase');
			
			self::$dsn = $driver . ':host=' . $host . ';dbname=' . $schema;
			self::$user = Config::get('Database','Username');
			self::$pass = Config::get('Database','Password');
			self::$db = new PDO(self::$dsn, self::$user, self::$pass);
			self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e)
		{
			throw new HydraDaoException ("Error (Dao::connect): " . $e->getMessage());
		}
	}

	/**
	 * Start a transaction
	 *
	 * @return bool
	 */
	public static function beginTransaction()
	{
		self::connect();
		return self::$db->beginTransaction();
	}
	
	/**
	 * Commit a transaction
	 *
	 * @return bool
	 */
	public static function commitTransaction()
	{
		self::connect();
		return self::$db->commit();
	}
	
	/**
	 * Rollback a transaction
	 *
	 * @return bool
	 */
	public static function rollbackTransaction()
	{
		self::connect();
		return self::$db->rollBack();
	}
	
	/**
	 * Convert an array into a set of objects defined by a DaoQuery instance
	 *
	 * @param DaoQuery $qry
	 * @param array $row
	 * @return HydraEntity
	 */
	public static function &objectify(DaoQuery $qry, array $row)
	{
		static $recurse = false;
		
		// Populate the focus object
		$fClass = $qry->getFocusClass();
		$focus = new $fClass;

		// We have to rebuild the objects in the order they appear in the sql results
		$i = 0;
		self::populateObject($qry, $focus, $fClass, $i, $row);
		
		foreach ($qry->getJoinClasses() as $joinClass)
		{
			list($joinClass, $joinField) = explode(':', $joinClass);
			
			// Check if we eager load this from the result set or from a sub query
			$resultsInArray = true;
			foreach (DaoMap::$map[strtolower($fClass)] as $field => $properties)
			{
				if ($field == '_')
				{
					continue;
				}
				
				if (isset($properties['rel']) and in_array($properties['rel'], array(DaoMap::MANY_TO_MANY, DaoMap::ONE_TO_MANY)))
				{
					if (isset($properties['class']) and $properties['class'] == $joinClass)
					{
						if (!$recurse)
						{
							$recurse = true;
							
							$q = new DaoQuery($properties['class']);
							
							if ($properties['rel'] == DaoMap::ONE_TO_MANY)
							{
								$criteria = sprintf('%sId = ?', strtolower(substr($fClass, 0, 1)) . substr($fClass, 1));
							}
							else
							{
								$prop = strtolower(substr($fClass, 0, 1)) . substr($fClass, 1) . 's';
								$otherProp = strtolower(substr($fClass, 0, 1)) . substr($fClass, 1);
								foreach(DaoMap::$map[strtolower($joinClass)] as $jcf => $jcp)
								{
									if(isset($jcp['rel']) && $jcp['rel'] == DaoMap::MANY_TO_MANY && $jcp['class'] == $fClass)
									{
										$prop = $jcf;
										break;
									}
								}
								$q->eagerLoad($joinClass . '.' . $prop);
								
								// JOIN TABLE ISSUE: MISSING JOIN TABLE NAME ALIAS FOR ID OF FIRST PARAM. $sId
								$criteria = sprintf('%sId = %s.id',
									$otherProp,
									DaoMap::$map[strtolower($joinClass)]['_']['alias']);
							}
							
							$r = self::findByCriteria($q, $criteria, array($row[0]));
							self::setProperty($focus, $field, $r);
						}
												
						$resultsInArray = false;
					}
				}
			}
			
			if ($resultsInArray)
			{
				// NOTE: From Tim, this may be required
				//$recurse = false;
				self::populateObject($qry, $focus, $joinClass, $i, $row, $joinField);
			}
		}
		
		// NOTE: From Tim, this may be required
		//$recurse = false;
		return $focus;
	}

	/**
	 * Load an instance of $class as a child of the $focus object with data beginning from $i offset in the $row array
	 *
	 * @param DaoQuery $qry
	 * @param HydraEntity $focus
	 * @param string $class
	 * @param int $i
	 * @param array $row
	 */
	private static function populateObject(DaoQuery &$qry, HydraEntity &$focus, $class, &$i, $row, $joinField=null)
	{
		if (get_class($focus) == $class)
		{
			$focus->setId($row[$i]);
			$i++;
		}
		
		foreach (DaoMap::$map[strtolower($class)] as $field => $properties)
		{
			if ($field == '_')
			{
				continue;
			}

			if (isset($properties['rel']))
			{
				if ($properties['rel'] == DaoMap::ONE_TO_ONE && !$properties['owner'])
				{
					continue;
				}
				
				if ($properties['rel'] == DaoMap::MANY_TO_ONE || ($properties['rel'] == DaoMap::ONE_TO_ONE))
				{
					$id = $row[$i];
					$cls = DaoMap::$map[strtolower($class)][$field]['class'];
					$value = new $cls;
					$value->setId($id);
					
					// Check if we are eager loading this object
					if (!in_array($properties['class'] . ':' . $field , $qry->getJoinClasses()))
					{
						// Otherwise create the child object as a proxy object
						$value->setProxyMode(true);
					}
				}
				else
				{
					continue;
				}
			}
			else
			{
				$value = $row[$i];
			}

			// Figure out which object we are working on
			if (get_class($focus) == $class)
			{
				// Add the value via the setter if it exists, otherwise assume there is a public property we can use
				self::setProperty($focus, $field, $value);
			}
			else
			{
				// Add the value via the setter if it exists, otherwise assume there is a public property we can use
				self::$LazyLoadingEnabled = false;
				$child = self::getProperty($focus, $joinField);
				self::setProperty($child, $field, $value);
				self::setProperty($focus, $joinField, $child);
				self::$LazyLoadingEnabled = true;
			}
			
			$i++;
		}
	}
	
	/**
	 * Internal function to calculate the paging stats for a paged select
	 *
	 * @param DaoQuery $qry
	 */
	private static function calculatePageStats(DaoQuery $qry, $results)
	{
		if ($qry->isPaged())
		{
			$sql = 'select found_rows()';
			$stmt = self::$db->prepare($sql);
			
			if (self::$Debug)
			{
				var_dump($sql);
				echo "<br />";
			}
			
			if (!$stmt->execute())
			{
				return;
			}
			
			$my = $stmt->fetch(PDO::FETCH_NUM);
			self::$totalRows = $my[0];
			
			list(self::$pageNumber, self::$pageSize) = $qry->getPageStats();
			
			self::$totalPages = ceil(self::$totalRows / self::$pageSize);
		}
		else
		{
			if (!is_array($results))
			{
				self::$pageNumber = 0;
				self::$totalRows = 0;
				self::$totalPages = 0;
			}
			else
			{
				self::$pageNumber = 1;
				self::$totalPages = 1;
				self::$totalRows = count($results);
				self::$pageSize = self::$totalRows;
			}
		}
	}
	
	/**
	 * Find all objects within a DaoQuery
	 *
	 * @param DaoQuery $qry
	 * @param int optional $outputFormat
	 * @return array[]HydraEntity
	 */
	public static function findAll(DaoQuery $qry, $outputFormat=null)
	{
		$tmpOutputFormat = self::$OutputFormat;
		
		if (!is_null($outputFormat))
		{
			self::$OutputFormat = $outputFormat;
		}
		
		self::connect();
		$sql = $qry->generateForSelect();

		$results = self::getResults($qry, $sql);
		
		self::$OutputFormat = $tmpOutputFormat;
		return $results;
	}
	
	/**
	 * Retrieve an entity from the database by its primary key
	 *
	 * @param DaoQuery $qry
	 * @param int $id
	 * @param int optional $outputFormat
	 * @return HydraEntity
	 */
	public static function findById(DaoQuery $qry, $id, $outputFormat=null)
	{
		$tmpOutputFormat = self::$OutputFormat;
		
		if (!is_null($outputFormat))
		{
			self::$OutputFormat = $outputFormat;
		}
		
		self::connect();
		DaoMap::loadMap($qry->getFocusClass());
		
		$oldAutoActive = self::$AutoActiveEnabled;
		self::$AutoActiveEnabled = false;
		$results = self::findByCriteria($qry, '`' . DaoMap::$map[strtolower($qry->getFocusClass())]['_']['alias'] . '`.`id`=?', array($id));
		self::$AutoActiveEnabled = $oldAutoActive;
		
		if (self::$OutputFormat == self::AS_XML)
		{
			$results = explode("\n", $results);
			unset($results[3]);
			unset($results[1]);
			$results = implode("\n", $results);
			self::$OutputFormat = $tmpOutputFormat;
			return $results;
		}

		self::$OutputFormat = $tmpOutputFormat;
		
		if (is_array($results) && sizeof($results) > 0)
		{
			return $results[0];
		}
		
		return null;
	}
	
	/**
	 * Retrieve an entity from the database with a modified where clause
	 *
	 * @param DaoQuery $qry
	 * @param int $id
	 * @param array[] $orderByParamsArray
	 * @param int optional $outputFormat
	 * @return HydraEntity
	 */
	public static function findByCriteria(DaoQuery $qry, $criteria, $params, array $orderByParams=array(), $outputFormat=null)
	{
		$tmpOutputFormat = self::$OutputFormat;
		
		if (!is_null($outputFormat))
		{
			self::$OutputFormat = $outputFormat;
		}
		
		self::connect();
		$qry = $qry->where($criteria);
	
		foreach ($orderByParams as $field => $direction)
			$qry = $qry->orderBy($field,$direction);
		
		$sql = $qry->generateForSelect();

		$results = self::getResults($qry, $sql, $params);
		
		self::$OutputFormat = $tmpOutputFormat;
		return $results;
	}
	
	/**
	 * Prepare a string of search terms ready for inclusion in a Dao::search
	 *
	 * @param string $searchString
	 * @return string
	 */
	public static function prepareSearchString($searchString)
	{
		// Break into search boundaries
		$terms = explode('|', $searchString);
		
		for ($i=0; $i<count($terms); $i++)
		{
			$firstChar = substr($terms[$i], 0, 1);
			$lastChar = substr($terms[$i], -1);
				
			// Escape regexp special characters (order is very important)
			$terms[$i] = str_replace(
				array('\\', '.', '+', '?', '[', '^', ']', '$', '(', ')', '{', '}', '=', '!', '<', '>', ':'),
				array('\\\\', '\.', '\+', '\?', '\[', '\^', '\]', '\$', '\(', '\)', '\{', '\}', '\=', '\!', '\<', '\>', '\:'),
				$terms[$i]);
			
			// Wildcards
			$terms[$i] = str_replace('*', '[a-zA-Z0-9\-]*', $terms[$i]);
			$terms[$i] = str_replace('%', '[a-zA-Z0-9\-]*', $terms[$i]);
			
			if (($firstChar != '*') && ($lastChar != '*'))
				continue;
			
			// We use a * on the end of the regexp because we might already be at the edge of the word 
			if ($firstChar != '*')
				$terms[$i] = '(^|[^a-zA-Z0-9\-]+)' . $terms[$i];
			
			// We use a * on the end of the regexp because we might already be at the edge of the word 
			if ($lastChar != '*')
				$terms[$i] .= '([^a-zA-Z0-9\-]+|$)';
		}
		
		// Combine the terms back into a single string
		$searchString = implode('|', $terms);
		
		// Just in case someone tries to use & to represent the word "and"
		$searchString = str_replace('&', '(&|and)', $searchString);
		$searchString = str_replace(' and ', ' (&|and) ', $searchString);
		
		return $searchString;
	}
	
	/**
	 * Search for entities matching a search string
	 *
	 * @param DaoQuery $qry
	 * @param string $searchString
	 * @param int optional $outputFormat
	 * @return array[]HydraEntity
	 */
	public static function search(DaoQuery $qry, $searchString, $outputFormat=null,$doRLike=true)
	{
		if (!isset(DaoMap::$map[strtolower($qry->getFocusClass())]['_']['search']))
		{
			throw new HydraDaoException($qry->getFocusClass() . '::__loadDaoMap requires DaoMap::setSearchFields() to enable searching for this entity');
		}
		
		$tmpOutputFormat = self::$OutputFormat;
		
		if (!is_null($outputFormat))
		{
			self::$OutputFormat = $outputFormat;
		}
				
		self::connect();
		
		// Prepare the search string
		if($doRLike)
			$searchString = self::prepareSearchString($searchString);
		else
			$searchString = '%'.$searchString.'%';
		
		// Load the query
		DaoMap::loadMap($qry->getFocusClass());
		
		$where = '';
//		$fLoop = true;
		$searchSQLFields = array();
		foreach (DaoMap::$map[strtolower($qry->getFocusClass())]['_']['search'] as $field)
		{
//			if (!$fLoop)
//			{
//				$where .= ' or ';
//			}

			// Separate the field into its class and property names
			$tmp = explode('.', $field);
			
			if (count($tmp) == 1)
			{
				$property = $tmp[0]; 
				$child = null;
			}
			else
			{
				$property = $tmp[0];
				$child = $tmp[1];
			}
			
			$fTable = strtolower($qry->getFocusClass());
			if (!isset(DaoMap::$map[$fTable][$property]['class']))
			{
				// This is a field on the focus table
				$table = DaoMap::$map[$fTable]['_']['alias'];
			}
			else
			{
				// Ensure the relationship is eager loaded before we add it to the search terms
				$qry = $qry->eagerLoad($qry->getFocusClass() . '.' . $property, 'left');
				
				$table = strtolower(DaoMap::$map[$fTable][$property]['alias']);
				$field = $child;
			}
			
			$searchSQLFields[] = '`' . $table . '`.`' . $field . '`';
		}

		if (count($searchSQLFields) > 0)
		{
			if($doRLike)
				$where = "concat_ws(' ', " . implode(',', $searchSQLFields) . ") rlike :search";
			else
				$where = "concat_ws(' ', " . implode(',', $searchSQLFields) . ") like :search";
				
			$qry->where('(' . $where . ')');
		}
		
		$qry->DefaultJoinType = 'left join';
		$sql = $qry->generateForSelect();
		$sql = str_replace('select ', 'select distinct ', $sql);
		$qry->DefaultJoinType = 'inner join';
		
		if (self::$Debug)
		{
			var_dump($sql);
			echo "<br />";
		}
		
		$stmt = self::$db->prepare($sql);
		//$stmt->bindValue(':search', $searchString, PDO::PARAM_STR);

		try
		{
			if (!self::execStatement($stmt, array('search'=>$searchString)))
			{
				return null;
			}
		}
		catch (PDOException $ex)
		{
			if (self::$Debug)
			{
				self::drawFancyError($sql, $params);
			}
			
			throw $ex;
		}
		
		$results = array();
		while ($my = $stmt->fetch(PDO::FETCH_NUM))
		{
			$result = self::objectify($qry, $my);

			switch (self::$OutputFormat)
			{
				case self::AS_XML:
					$result = self::formatAsXml($result);
					break;
					
				case self::AS_ARRAY:
					$result = self::formatAsArray($result);
					break;
					
				default:
					break;
			}
			
			$results[] = $result;
		}
		
		self::calculatePageStats($qry, $results);
		
		self::$OutputFormat = $tmpOutputFormat;
		return $results;
	}
	
	/**
	 * Save an entity into the database
	 *
	 * @param HydraEntity $entity
	 * @return bool
	 */
	public static function save(HydraEntity $entity)
	{
		$entity->preSave();
		
		$logs = $entity->collectLogs();
		if(sizeof($logs) > 0)
		{
			foreach($logs as $log)
			{
				$behalfOfUserAccountId = null;
				$behalfOfRoleId = null;
				if (count($log) > 8)
					list($tentityId,$tentity,$field,$newValue,$oldValue,$comment,$createdSiteTime,$fieldEntity,$behalfOfUserAccount,$behalfOfRole) = $log;
				else
					list($tentityId,$tentity,$field,$newValue,$oldValue,$comment,$createdSiteTime,$fieldEntity) = $log;
				Logging::LogEntityChange($tentityId,$tentity,$field,$newValue,$oldValue,$comment,$createdSiteTime,$fieldEntity,$behalfOfUserAccount,$behalfOfRole);
			}
			
			if (method_exists($entity, 'onLog'))
				$entity->onLog($logs);
		}	
		
		if (is_null($entity->getId()))
		{
			// Insert a new record
			$id = self::insert($entity);
			$entity->postSave($id,true);
			return $id;
			
		}
		else
		{			
			// Check if the entity needs to be versioned or not
			if ($entity instanceof HydraVersionedEntity)
			{
				$oldVersionId = $entity->getId();
				
				// The entity that we are saving becomes the new child 
				$c = get_class($entity);
				$parent = new $c();
				$parent->setProxyMode(true);
				$parent->setId($oldVersionId);
				
				$entity->setId(null);
				$entity->setPreviousVersion($parent);

				// Save the new versioned entity
				$returnValue = self::insert($entity);
				
				// Fixed bug for updated time, as it was taking local sql server time instead of utc
				$nowUTC = new HydraDate();
				$sql = sprintf('update %s set active=0, updated=\'%s\' where id=%d', strtolower(get_class($entity)), $nowUTC->__toString(), $oldVersionId);
				
				self::execSql($sql);
				
				$entity->postSave($returnValue,false);
				
				// Check all related entities to see if we need to version them too
				self::versionAffectedChildren($entity, $parent);
				
				return $returnValue;
			}
			else
			{
				// Update an existing record
				$id = self::update($entity);
				$entity->postSave($id,false);
				return $id;
			}
		}
	}
	
	private static function versionAffectedChildren(HydraEntity $entity, HydraEntity $parent)
	{
		$map = DaoMap::$map[strtolower(get_class($entity))];
		
		foreach ($map as $field => $p)
		{
			if ($field == '_')
			{
				continue;
			}
			
			// If there is a many-to-many relationship attached to this entity, then we need to version that join data regardless
			if (isset($p['rel']) && $p['rel'] == DaoMap::MANY_TO_MANY)
			{
				// Join in the many to many join table
				if ($p['side'] == DaoMap::RIGHT_SIDE)
				{
					$mtmJoinTable = strtolower(get_class($entity) . '_' . $p['class']); 
				}
				else
				{
					$mtmJoinTable = strtolower($p['class'] . '_' . get_class($entity)); 
				}
				
				$targetField = strtolower(substr($p['class'], 0, 1)) . substr($p['class'], 1);
				$sourceField = strtolower(substr(get_class($entity), 0, 1)) . substr(get_class($entity), 1);
				
				$sql = sprintf('select %sId from %s where %sId=%d',
					$targetField,
					$mtmJoinTable,
					$sourceField,
					$parent->getId());
					
				$stmt = self::execSql($sql);
						
				while ($my = $stmt->fetch(PDO::FETCH_NUM))
				{
					$sql = sprintf('insert into %s (%sId, %sId, createdById) values (%d, %d, %d)',
						$mtmJoinTable,
						$sourceField,
						$targetField,
						$entity->getId(),
						$my[0],
						Core::getUser()->getId());
						
					self::execSql($sql);
				}
			}
		}
	}
	
	/**
	 * Insert an entity into the database
	 *
	 * @param HydraEntity $entity
	 * @return bool|int
	 */
	public static function insert(HydraEntity &$entity)
	{
		self::connect();
		
		if (!is_null($entity->getId()))
		{
			throw new HydraDaoException('Entity has already been persisted');
		}
		
		// Add versioning data to entity
		$entity->setCreatedBy(Core::getUser());
		$entity->setUpdatedBy(Core::getUser());
		
		$entity->setActive(1);
		$entity->setCreated(new HydraDate());
		$entity->setUpdated(new HydraDate());
		
		$qry = new DaoQuery(get_class($entity));
		$sql = $qry->generateForInsert();
		
		if (self::$Debug)
		{
			var_dump($sql);
			echo "<br />";
		}
		
		$stmt = self::$db->prepare($sql);
		$data = array();
		
		foreach (DaoMap::$map[strtolower($qry->getFocusClass())] as $field => $properties)
		{
			if ($field == '_')
			{
				continue;
			}
			
			if (isset($properties['rel']))
			{
				if (($properties['rel'] == DaoMap::MANY_TO_ONE) || ($properties['rel'] == DaoMap::ONE_TO_ONE && $properties['owner']))
				{
					$child = self::getProperty($entity, $field);
					
					if($child != null)
					{
						$data[] = $child->getId();
					}
					else
					{
						$data[] = null;
					}
				}
				else
				{
					continue;
				}
			}
			else
			{
				$d = self::getProperty($entity, $field);
				
				if ($properties['type'] == 'bool')
				{
					$d = intval($d);
				}
				
				$data[] = $d;
			}
		}		

		try
		{
			if ($id = self::execStatement($stmt, $data))
			{
				// Grab the last insert id for this record and attach it to the entity
//				$id = self::$db->lastInsertId();
				$entity->setId($id);
				
				if (method_exists($entity, 'onInsert'))
					$entity->onInsert();
									
				return $id;
			}
		}
		catch (PDOException $ex)
		{
			if (self::$Debug)
			{
				self::drawFancyError($sql, $data);
			}
			
			throw $ex;
		}
		
		return false;
	}
	
	/**
	 * Update an entity in the database
	 *
	 * @param HydraEntity $entity
	 * @return bool
	 */
	public static function update(HydraEntity &$entity)
	{
		self::connect();
		
		if (is_null($entity->getId()))
		{
			throw new HydraDaoException('Entity has not yet been persisted');
		}
		
		// Add versioning data to entity
		// Fixed bug for updated time, as it was taking local sql server time instead of utc
		$entity->setUpdated(new HydraDate());
		
		$entity->setUpdatedBy(Core::getUser());
		
		$qry = new DaoQuery(get_class($entity));
		$sql = $qry->generateForUpdate();

		if (self::$Debug)
		{
			var_dump($sql);
			echo "<br />";
		}
		
		$stmt = self::$db->prepare($sql);
		$data = array();
		
		foreach (DaoMap::$map[strtolower($qry->getFocusClass())] as $field => $properties)
		{
			if ($field == '_')
			{
				continue;
			}

			if (isset($properties['rel']))
			{
				if ($properties['rel'] == DaoMap::MANY_TO_ONE or ($properties['rel'] == DaoMap::ONE_TO_ONE and $properties['owner']))
				{
					$child = self::getProperty($entity, $field);
					if($child != null)
						$data[] = $child->getId();
					else
						$data[] = null;
				}
				else
				{
					continue;
				}
			}
			else
			{
				$d = self::getProperty($entity, $field);
				
				if ($properties['type'] == 'bool')
				{
					$d = intval($d);
				}
				
				$data[] = $d;
			}
		}	

		$data[] = $entity->getId();

		$retVal = self::execStatement($stmt, $data);
		
		return $retVal;
	}
	
	/**
	 * Delete an entity from the database
	 *
	 * @param HydraEntity $entity
	 * @return bool
	 */
	public static function delete(HydraEntity $entity)
	{
		return self::deactivate($entity);
	}
	
	public static function activate(HydraEntity $entity)
	{
		self::connect();
		
		if (is_null($entity->getId()))
		{
			throw new HydraDaoException('Entity has not yet been persisted');
		}
		
		// Fixed bug for updated time, as it was taking local sql server time instead of utc
		$nowUTC = new HydraDate();		
		$sql = sprintf('update %s set active=1, updated=\'%s\', updatedById=? where id=?', strtolower(get_class($entity)), $nowUTC->__toString());
		
		if (self::$Debug)
		{
			var_dump($sql);
			echo "<br />";
		}
		
		$stmt = self::$db->prepare($sql);
		$retVal = self::execStatement($stmt, array(Core::getUser()->getId(), $entity->getId()));
		
		if ($retVal !== false)
		{
			$entity->setActive(1);
		}
		
		return $retVal;
	}
	
	public static function deactivate(HydraEntity $entity)
	{
		self::connect();
		
		if (is_null($entity->getId()))
		{
			throw new HydraDaoException('Entity has not yet been persisted');
		}
		
		// Fixed bug for updated time, as it was taking local sql server time instead of utc
		$nowUTC = new HydraDate();
		$sql = sprintf('update %s set active=0, updated=\'%s\', updatedById=? where id=?', strtolower(get_class($entity)), $nowUTC->__toString());
		
		if (self::$Debug)
		{
			var_dump($sql);
			echo "<br />";
		}
		
		$stmt = self::$db->prepare($sql);
		$retVal = self::execStatement($stmt, array(Core::getUser()->getId(), $entity->getId()));
		
		if ($retVal !== false)
		{
			$entity->setActive(0);
		}
		
		return $retVal;
	}

	/**
	 * Run an SQL statement and return the PDOStatement object
	 *
	 * @param string $sql
	 * @param array $params
	 * @return PDOStatement
	 */
	public static function execSql($sql, array $params=array())
	{
		self::connect();
		
		if (self::$Debug)
		{
			var_dump($sql);
			echo "<br />";
		}
		
		$stmt = self::$db->prepare($sql);
		$retVal = self::execStatement($stmt, $params);
		if(is_numeric($retVal))
			self::$lastInsertId=$retVal;
		return $stmt;
	}
	
	public static function execStatement(PDOStatement $stmt, $params=array())
	{
		self::connect();

		$retVal = null;
		try
		{
			// Is this a profiler query?
			$isProfilerQuery = (strpos($stmt->queryString, 'insert into profile') === false) ? false : true;
			
			// Start the profiler
			if (!$isProfilerQuery)
			{
				$profiler = new Profiler((bool)Config::get('Profiler', 'SQL'));
				$profiler->Sql = $stmt->queryString;
				$profiler->SqlArgs = $params;
				$profiler->start();
			}
			
			$retVal = $stmt->execute($params);
			
			// If this is an insert statement, then we need to capture the last insert id,
			// otherwise the profiler overrides it if activated
			if ($retVal && (strtolower(substr($stmt->queryString, 0, 6)) == 'insert'))
			{
				$retVal = self::$db->lastInsertId();
			}
			
			// Stop the profiler
			if (!$isProfilerQuery)
			{
				$profiler->stop();
			}
		}
		catch (PDOException $ex)
		{
			if(strstr($ex->getMessage(), '|HYDRA_TRIGGER_ERROR_31|') === false)
			{
				if (self::$Debug)
				{
					self::drawFancyError($stmt->queryString, $params);
				} else {
					throw $ex;
				}
			}
			else
			{
				$array = explode('|', $ex->getMessage());
				
				if(sizeof($array) == 4)
				{
					$type = $array[2];
					$param = $array[3];
					throw new $type($param);
				} else {
					throw $ex;
				}
			}
		}		
		return $retVal;
	}

	/**
	 * Run a native SQL statement against the database and return the record
	 *
	 * @param string $sql
	 * @param array $params
	 * @return array[]mixed
	 */
	public static function getSingleResultNative($sql, array $params=array())
	{
		$stmt = self::execSql($sql, $params);
		$my = $stmt->fetch(PDO::FETCH_NUM);
		return $my;
	}

	/**
	 * Run a native SQL statement against the database and return the result set
	 *
	 * @param string $sql
	 * @param array $params
	 * @return array[]array[]mixed
	 */
	public static function getResultsNative($sql, array $params=array(),$fetch=PDO::FETCH_NUM)
	{
		$stmt = self::execSql($sql, $params);
		$results = $stmt->fetchAll($fetch);
		return $results;
	}
	
	/**
	 * Retrieve a single record from the database as an entity structure
	 *
	 * @param DaoQuery $qry
	 * @param string $sql
	 * @param array[]mixed $params
	 * @return HydraEntity
	 */
	public static function getSingleResult(DaoQuery $qry, $sql, array $params=array())
	{
		$stmt = self::execSql($sql, $params);
				
		$my = $stmt->fetch(PDO::FETCH_NUM);
		
		if(!is_array($my))
			return null;
		
		$result = self::objectify($qry, $my);
		
		switch (self::$OutputFormat)
		{
			case self::AS_XML:
				$result = self::formatAsXml($result);
				break;
				
			case self::AS_ARRAY:
				$result = self::formatAsArray($result);
				break;
				
			default:
				break;
		}
		
		return $result;
	}

	/**
	 * Retrieve a list of records from the database and convert the output to entities
	 *
	 * @param DaoQuery $qry
	 * @param string $sql
	 * @param array[]mixed $params
	 * @return array[]HydraEntity
	 */
	public static function getResults(DaoQuery $qry, $sql, array $params=array())
	{
		$stmt = self::execSql($sql, $params);
				
		$results = array();
		while ($my = $stmt->fetch(PDO::FETCH_NUM))
		{
			$result = self::objectify($qry, $my);
			
			switch (self::$OutputFormat)
			{
				case self::AS_XML:
					$result = self::formatAsXml($result);
					$tmp = explode("\n", $result);
					$xmlHeader = trim($tmp[0]);
					$result = trim($tmp[1]);
					break;
					
				case self::AS_ARRAY:
					$result = self::formatAsArray($result);
					break;
					
				default:
					break;
			}
			
			$results[] = $result;
		}
		
		self::calculatePageStats($qry, $results);
		
		if (self::$OutputFormat == self::AS_XML)
		{
			$results = implode("\n", $results);
			$results = $xmlHeader . "\n<resultset>\n" . $results . "\n</resultset>";
		}
		
		return $results;
	}
	
	/**
	 * Convert an entity into an XML string
	 *
	 * @param HydraEntity $entity
	 * @return string
	 */
	public static function formatAsXml(HydraEntity $entity)
	{
		return XmlObjectConverter::toXml($entity);
	}
	
	/**
	 * Convert an entity into an array
	 *
	 * @param HydraEntity $entity
	 * @return array
	 */
	public static function formatAsArray(HydraEntity $entity)
	{
		return ArrayObjectConverter::toArray($entity);
	}

	/**
	 * Draw a fancy box on the screen containing info about the query that was run
	 *
	 * @param string $sql
	 * @param array $data
	 */
	private static function drawFancyError($sql, $data)
	{
		echo "<div style=\"border:1px solid #000;padding:5px;background-color:#ffffcc;\"><b>An error occured while trying to run the following query:</b><br /><br />\n";
		var_dump($sql);
		echo "<br />\n";
		var_dump($data);
		echo "</div>";
	}
}

?>