<?php
/**
 * Organization Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Organization extends EncryptedEntityAbstract
{
    /**
     * The username
     *
     * @var string
     */
    private $name;
    /**
     * Getter for name
     *
     * @return 
     */
    public function getName() 
    {
        return $this->name;
    }
    /**
     * Setter for name
     *
     * @param string $value The name
     *
     * @return Organization
     */
    public function setName($value) 
    {
        $this->name = $value;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
    	DaoMap::begin($this, 'org');
    	DaoMap::setStringType('name', 'varchar', 100);
    	DaoMap::setStringType('skey', 'varchar', 32);
    	parent::__loadDaoMap();
    
    	DaoMap::createIndex('name');
    	DaoMap::createIndex('skey');
    	DaoMap::commit();
    }
    /**
     * Creating a Organization
     * 
     * @param string $name
     * 
     * @return Ambigous <BaseEntityAbstract, GenericDAO>
     */
    public function create($name)
    {
    	$item = new Organization();
    	return $item->setName(trim($name))
    		->save();
    }
}

?>
