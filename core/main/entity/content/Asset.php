<?php
/**
 * Entity for tracking location of Asset assets in shared storage
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Asset extends EncryptedEntityAbstract
{
	/**
	 * @var string
	 */
	private $filename;
	/**
	 * @var string
	 */
	private $mimeType;
	/**
	 * The content of this asset
	 * 
	 * @var string
	 */
	protected $content;
	/**
	 * getter filename
	 *
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}
	/**
	 * setter filename
	 * 
	 * @param string $filename The filename of the asset
	 * 
	 * @return Asset
	 */
	public function setFilename($filename)
	{
		$this->filename = $filename;
		return $this;
	}
	/**
	 * getter mimeType
	 *
	 * @return string
	 */
	public function getMimeType()
	{
		return $this->mimeType;
	}
	/**
	 * setter mimeType
	 * 
	 * @param string $mimeType The mimeType
	 * 
	 * @return Asset
	 */
	public function setMimeType($mimeType)
	{
		$this->mimeType = $mimeType;
		return $this;
	}
	/**
	 * Getter for content
	 *
	 * @return Content
	 */
	public function getContent() 
	{
		$this->loadManyToOne('content');
	    return $this->content;
	}
	/**
	 * Setter for content
	 *
	 * @param Content $value The content
	 *
	 * @return Asset
	 */
	public function setContent($value) 
	{
	    $this->content = $value;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::__toString()
	 */
	public function __toString()
	{
	    return $this->getFilename();
	}
	/**
	 * (non-PHPdoc)
	 * @see HydraEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'asset');
		
		DaoMap::setStringType('filename', 'varchar', 100);
		DaoMap::setStringType('mimeType', 'varchar', 50);
		DaoMap::setStringType('content');
		parent::__loadDaoMap();
		
		DaoMap::commit();
	}
	/**
	 * Remove an asset from the content server
	 *
	 * @param array $skeys The assetids of the content
	 *
	 * @return bool
	 */
	public static function removeAssets(array $skeys)
	{
		if(count($skeys) === 0)
			return true;
		
		$where = 'skey in (' . implode(', ', array_fill(0, count($skeys), '?')) . ')';
		$params = $skeys;
		//delete the contents
		if(count($contentIds = array_map(create_function('$a', 'return $a->getContent()->getId();'), self::getAllByCriteria($where, $params))) > 0 )
			Content::deleteByCriteria('id in (' . implode(', ', array_fill(0, count($contentIds), '?')) . ')', $contentIds);
		// Delete the item from the database
		self::deleteByCriteria($where, $params);
		return true;
	}
	/**
	 * Register a file with the Asset server and get its asset id
	 *
	 * @param string $filename The name of the file
	 * @param string $data     The data within that file we are trying to save
	 *
	 * @return string 32 char MD5 hash
	 */
	public static function registerAsset($filename, $dataOrFile)
	{
		if(!is_string($dataOrFile) && (!is_file($dataOrFile)))
			throw new CoreException(__CLASS__ . '::' . __FUNCTION__ . '() will ONLY take string to save!');
		
		$class = get_called_class();
		$asset = new $class();
		$asset->setFilename($filename)
			->setMimeType(StringUtilsAbstract::getMimeType($filename))
			->setContent(Content::create(is_file($dataOrFile) ? readfile($dataOrFile) : $dataOrFile))
			->save();
		return $asset;
	}
}

?>