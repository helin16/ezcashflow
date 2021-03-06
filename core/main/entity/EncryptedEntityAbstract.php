<?php
/**
 * EncryptedEntityAbstract entity class
 *
 * @package Core
 * @subpackage Entity
 */
abstract class EncryptedEntityAbstract extends BaseEntityAbstract
{
	/**
     * The secret key for the organization
     *
     * @var string
     */
    protected $skey = '';
    /**
     * Getter for skey
     *
     * @return string
     */
    public function getSkey()
    {
    	return $this->skey;
    }
    /**
     * Setter for skey
     *
     * @param string $value The skey
     *
     * @return Organization
     */
    public function setSkey($value)
    {
    	$this->skey = $value;
    	return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntityAbstract::preSave()
     */
    public function preSave()
    {
    	parent::preSave();
    	if(trim($this->getSkey()) === '') {
    		$this->setSkey(self::genSkey($this));
    	}
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
    	DaoMap::setStringType('skey', 'varchar', 32);
    	parent::__loadDaoMap();

    	DaoMap::createUniqueIndex('skey');
    }
    /**
     * Generating the skey
     *
     * @param EncryptedEntityAbstract $entity
     *
     * @return string
     */
    public static function genSkey(EncryptedEntityAbstract $entity)
    {
    	return md5(get_class($entity) . '_' . $entity->getId() . '_' . trim(UDate::now()) . '_' . rand(0, PHP_INT_MAX));
    }
    /**
     * Getting the EncryptedEntityAbstract object
     *
     * @param string $skey The skey of the object
     *
     * @return EncryptedEntityAbstract
     */
    public static function getBySkey($skey)
    {
    	$items = self::getAllByCriteria('skey = ?', array(trim($skey)), false, 1, 1);
    	return count($items) === 0 ? null : $items[0];
    }
}

?>
