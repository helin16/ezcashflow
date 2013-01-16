<?php
/**
 * AssetType Entity - This is the type of the Asset
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class AssetType extends BaseEntityAbstract
{
    /**
     * ID of the type for graph
     * 
     * @var string
     */
    const ID_GRAPH = 1;
    /**
     * ID of the type for report
     * 
     * @var string
     */
    const ID_REPORT = 2;
    /**
     * ID of the type for doc
     * 
     * @var string
     */
    const ID_DOC = 3;
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $path;
    /**
     * getter type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * setter type
     * 
     * @param string $type
     * 
     * @return AssetType
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    /**
     * getter path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    /**
     * setter path
     * 
     * @param string $path The filepath of the type
     * 
     * @return AssetType
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntityAbstract::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'at');
    
        DaoMap::setStringType('type', 'varchar', 10);
        DaoMap::setStringType('path', 'varchar', 255);
    
        DaoMap::commit();
    }
}