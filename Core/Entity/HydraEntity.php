<?php

/**
 * Common entity class
 *
 * @package Core
 * @subpackage Entity
 */
class HydraEntity
{
	public static $keepLogs = true;
	
	/**
	 * Internal id used by all application entities
	 * 
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var bool
	 */
	protected $active;
	
	/**
	 * @var HydraDate
	 */
	protected $created;
	
	/**
	 * @var UserAccount
	 */
	protected $createdBy;

	/**
	 * @var HydraDate
	 */
	protected $updated;
	
	/**
	 * @var UserAccount
	 */
	protected $updatedBy;
	
	/**
	 * Validation rules for this entity
	 *
	 * @var array[]array[]HydraBaseValidator
	 */
	private $validationRules = array();

	/**
	 * Is this a proxy object?
	 * 
	 * @var bool
	 */
	protected $proxyMode = false;
	
	private $logEntries = array();
	
	protected function addLog($field,$newValue,$oldValue,$comment="",$createdSiteTime="",$fieldEntity="", $behalfOfUserAccount=null, $behalfOfRole=null)
	{
		if(!HydraEntity::$keepLogs)
			return;
		
		if($this->getId() == null)
			return;

		if($oldValue instanceof HydraEntity)
		{
			$old = $oldValue->getId();
		} else {
			$old = (string)$oldValue;	
		}
		
		if($newValue instanceof HydraEntity)
		{
			$new = $newValue->getId();
		} else {
			$new = (string)$newValue;
		}
						
		if($old != $new)
		{
			$this->logEntries[] = array($this->getId(),get_class($this),$field,$new,$old,$comment,$createdSiteTime,$fieldEntity,$behalfOfUserAccount, $behalfOfRole);	
		}			
	}
	
	public function collectLogs()
	{
		$logs = $this->logEntries;
		$this->logEntries = array();
		return $logs;
	}
	
	/**
	 * Set the primary key for this entity
	 *
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}
	
	/**
	 * Get the primary key for this entity
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Set when this entity was created
	 *
	 * @param string $created
	 */
	public function setCreated($created)
	{
		$this->created = $created;
	}
	
	/**
	 * When was this entity created
	 *
	 * @return HydraDate
	 */
	public function getCreated()
	{
		if (is_string($this->created))
		{
			$this->created = new HydraDate($this->created);
		}
		
		return $this->created;
	}
	
	/**
	 * Set who created this entity
	 *
	 * @param UserAccount $user
	 */
	public function setCreatedBy(UserAccount $user)
	{
		$this->createdBy = $user;
	}
	
	/**
	 * Who created this entity
	 *
	 * @return UserAccount
	 */
	public function getCreatedBy()
	{
		$this->loadManyToOne('createdBy');
		return $this->createdBy;
	}
	
	/**
	 * Set when this entity was last updated
	 *
	 * @param string $updated
	 */
	public function setUpdated($updated)
	{
		$this->updated = $updated;
	}
	
	/**
	 * When was this entity last updated
	 *
	 * @return HydraDate
	 */
	public function getUpdated()
	{
		if (is_string($this->updated))
		{
			$this->updated = new HydraDate($this->updated);
		}
		
		return $this->updated;
	}
	
	/**
	 * Set who last updated this entity
	 *
	 * @param UserAccount $user
	 */
	public function setUpdatedBy(UserAccount $user)
	{
		$this->updatedBy = $user;
	}
	
	/**
	 * Who last updated this entity
	 *
	 * @return UserAccount
	 */
	public function getUpdatedBy()
	{
		$this->loadManyToOne('updatedBy');
		return $this->updatedBy;
	}
	
    /**
     * @return bool
     */
    public function isActive()
    {
    	if($this->active == 0)
    	{
    		return false;
    	}
    	else
    	{
    		return true;
    	}
    }
    
    public function setActive($active)
    {
    	$this->active = intval($active);
    }
    
    /**
     * @return int
     */
    public function getActive()
    {
    	return $this->active;
    }
	
	/**
	 * Add a validation rule to the entity
	 *
	 * @param string $field
	 * @param HydraBaseValidator $rule
	 */
	public function addValidation($field, HydraBaseValidator $rule)
	{
		$this->validationRules[$field][] = $rule;
	}

	/**
	 * Get the validation rules for a property of this entity
	 *
	 * @param string $field
	 * @return array[]HydraBaseValidator
	 */
	public function getValidations($field)
	{
		return $this->validationRules[$field];
	}
	
	/**
	 * Validate a field on this entity according to the attached validation rules
	 *
	 * @param string $field
	 * @return array[]string
	 */
	public function validate($field)
	{
		$passed = true;
		$messages = array();
		
		foreach ($this->validationRules[$field] as $validation)
		{
			$method = 'get' . ucwords($field);
			if (!$validation->validate($this->$method()))
			{
				$passed = false;
				$messages[] = $validation->getMessage();
			}
		}
		
		if ($passed)
		{
			return true;
		}
		
		// Validation failed so return an array of error messages
		return $messages;
	}
	
	/**
	 * Validate all fields on this entity
	 *
	 * @return array[]string
	 */
	public function validateAll()
	{
		$passed = true;
		$messages = array();
		
		foreach ($this->validationRules as $field => $rules)
		{
			$returnMessages = $this->validate($field);
			
			if (is_array($returnMessages))
			{
				$passed = false;
				foreach ($returnMessages as $message)
				{
					$messages[] = $message;
				}
			}
		}

		if ($passed)
		{
			return true;
		}
		
		// Validation failed so return an array of error messages
		return $messages;
	}

	/**
	 * Dictates if the entity is a proxy object or not for lazy loading purposes
	 */
	public function setProxyMode($bool)
	{
		$this->proxyMode = $bool;
	}
	
	/**
	 * Check if an entity is a proxy object
	 *
	 * @return bool
	 */
	public function getProxyMode()
	{
		return $this->proxyMode;
	}
	
	/**
	 * Default behaviour for an entity dao map
	 */
	public function __loadDaoMap()
	{
		throw new HydraDaoException(get_class($this) . '::__loadDaoMap() is unimplemented');
	}
	
	/**
	 * Lazy load a one-to-many relationship 
	 *
	 * @param string $property
	 */
	protected function loadOneToMany($property)
	{
		if (!Dao::$LazyLoadingEnabled)
		{
			return;
		}
		
		Dao::$LazyLoadInProgress = true;
		
		// Figure out what the object type is on the many side
		$this->__loadDaoMap();
		$thisClass = get_class($this);
		$cls = DaoMap::$map[strtolower($thisClass)][$property]['class'];

		DaoMap::loadMap($cls);
		$alias = DaoMap::$map[strtolower($cls)]['_']['alias'];
		$field = strtolower(substr($thisClass, 0, 1)) . substr($thisClass, 1);
		$this->$property = Dao::findByCriteria(new DaoQuery($cls), sprintf('%s.`%sId`=?', $alias, $field), array($this->getId()));
		
		Dao::$LazyLoadInProgress = false;
	}

	/**
	 * Lazy load a one-to-one relationship 
	 *
	 * @param string $property
	 */
	protected function loadOneToOne($property)
	{
		return $this->loadManyToOne($property);
	}
	
	/**
	 * Lazy load a many-to-one relationship 
	 *
	 * @param string $property
	 */
	protected function loadManyToOne($property)
	{
		if (!Dao::$LazyLoadingEnabled)
		{
			return;
		}
		
		if (is_null($this->$property))
		{
			$this->__loadDaoMap();
			if(DaoMap::$map[strtolower(get_class($this))][$property]['nullable'])
			{
				$this->$property = null;
				return;
			} else
				throw new Exception('Property (' . get_class($this) . '::' . $property . ') must be initialised to integer or proxy prior to lazy loading', 1);
		}
		
		Dao::$LazyLoadInProgress = true;

		// If the entity property is not an entity already, then we need to convert it
		
		// Load the DAO map for this entity
		$this->__loadDaoMap();
		$cls = DaoMap::$map[strtolower(get_class($this))][$property]['class'];
		
		if (!$this->$property instanceof HydraEntity)
		{
			$this->$property = new $cls;
			$this->$property->setProxyMode(true);
			
			$this->$property = Dao::findById(new DaoQuery($cls), $this->$property);
		}
		else
		{
			// Lazy load it up from the DAO
			if ($this->$property->getProxyMode())
			{
				// New entities are automatically created as non-proxy objects
				$this->$property = Dao::findById(new DaoQuery($cls), $this->$property->getId());
			}
		}
		
		Dao::$LazyLoadInProgress = false;
	}
	
	/**
	 * Lazy load a many-to-many relationship 
	 *
	 * @param string $property
	 */
	protected function loadManyToMany($property)
	{
		if (!Dao::$LazyLoadingEnabled)
		{
			return;
		}
		
		Dao::$LazyLoadInProgress = true;
		
		// Grab the DaoMap data for both ends of the join
		$this->__loadDaoMap();
		$cls = DaoMap::$map[strtolower(get_class($this))][$property]['class'];
		$obj = new $cls;
		$obj->__loadDaoMap();

		$thisClass = get_class($this);

		$qry = new DaoQuery($cls);
		
		$qry->eagerLoad($cls . '.' . strtolower(substr($thisClass, 0, 1)) . substr($thisClass, 1) . 's');
		
		// Load this end with an array of entities typed to the other end
		DaoMap::loadMap($cls);
		$alias = DaoMap::$map[strtolower($cls)]['_']['alias'];
		$field = strtolower(substr($thisClass, 0, 1)) . substr($thisClass, 1);
		$this->$property = Dao::findByCriteria($qry, sprintf('`%sId`=?', $field), array($this->getId()));
		
		Dao::$LazyLoadInProgress = false;
	}
	
	public function preSave()
	{
		return;
	}
	
	public function postSave($id,$isInsert)
	{
		return;
	}
	
	/**
	 * This behaviour is blocked
	 *
	 * @param string $var
	 */
	public function __get($var)
	{
		$class = get_class($this);
		throw new Exception("Attempted to get variable $class::$var directly and it is either inaccessable or doesnt exist");
	}
	
	/**
	 * This behaviour is blocked
	 *
	 * @param string $var
	 * @param mixed $value
	 */
	public function __set($var, $value)
	{
		$class = get_class($this);
		throw new Exception("Attempted to set variable $class::$var directly and it is either inaccessable or doesnt exist");
	}	
	
	public function getString()
	{
		return $this->__toString();
	}
	
	/**
	 * Default toString implementation
	 *
	 * @return string
	 */
	public function __toString()
	{
		return get_class($this) . ' (#' . $this->getId() . ')';
	}
}

?>