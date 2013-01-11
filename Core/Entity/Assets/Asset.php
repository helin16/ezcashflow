<?php
/**
 * Asset Entity - This is the file holder or register
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Asset extends BaseEntityAbstract
{
    /**
     * Linked transactions
     * 
     * @var array[Transaction]
     */
    protected $transactions;
    /**
     * Linked Property
     * 
     * @var array[Property]
     */
    protected $propertys;
	/**
	 * @var AssetType
	 */
	protected $assetType;
	/**
	 * @var string
	 */
	private $assetKey;
	/**
	 * @var string
	 */
	private $filename;
	/**
	 * @var string
	 */
	private $mimeType;
	/**
	 * @var string
	 */
	private $path;
	/**
	 * getter assetType
	 *
	 * @return AssetType
	 */
	public function getAssetType()
	{
		$this->loadManyToOne('assetType');
		return $this->assetType;
	}
	/**
	 * setter assetType
	 * 
	 * @param AssetType $assetType The new AssetType
	 * 
	 * @return Asset
	 */
	public function setAssetType(AssetType $assetType)
	{
		$this->assetType = $assetType;
		return $this;
	}
	/**
	 * getter assetKey
	 *
	 * @return string
	 */
	public function getAssetKey()
	{
		return $this->assetKey;
	}
	/**
	 * setter assetKey
	 * 
	 * @param string $assetKey The asset ID(hash string)
	 * 
	 * @return Asset
	 */
	public function setAssetKey($assetKey)
	{
		$this->assetKey = $assetKey;
		return $this;
	}
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
	 * @param string $filename The file name of the asset
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
	 * @param string $mimeType The mime type of the asset
	 * 
	 * @return Asset
	 */
	public function setMimeType($mimeType)
	{
		$this->mimeType = $mimeType;
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
	 * @param string $path The file path of the asset
	 * 
	 * @return Asset
	 */
	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}
	/**
	 * Getting the linked Transactions
	 * 
	 * @return multitype:Transaction
	 */
	public function getTransactions()
	{
	    $this->loadManyToMany('transactions');
	    return $this->transactions;
	}
	/**
	 * Setting the linked Transactions
	 * 
	 * @param array $transactions The linked Transactions
	 * @return Asset
	 */
	public function setTransactions($transactions)
	{
	    $this->transactions = $transactions;
	    return $this;
	}
	/**
	 * Getting the linked Property
	 * 
	 * @return multitype:Property
	 */
	public function getPropertys()
	{
	    $this->loadManyToMany('propertys');
	    return $this->propertys;
	}
	/**
	 * Getting the linked Property
	 * 
	 * @param array $propertys The linked property
	 * 
	 * @return Asset
	 */
	public function setPropertys($propertys)
	{
	    $this->propertys = $propertys;
	    return $this;
	}
	/**
	 * Getting the file path of the content
	 * 
	 * @return The file path
	 */
	public function getFilePath()
	{
	    return $this->getAssetType()->getPath() . $this->getPath() . '/' . $this->getAssetKey();
	}
	/**
	 * (non-PHPdoc)
	 * @see HydraEntity::__toString()
	 */
	public function __toString()
	{
		return $this->filename;
	}
	/**
	 * (non-PHPdoc)
	 * @see HydraEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'con');
		
		DaoMap::setManyToOne('assetType', 'AssetType', 'at');
		DaoMap::setStringType('assetKey', 'varchar', 32);
		DaoMap::setStringType('filename', 'varchar', 100);
		DaoMap::setStringType('mimeType', 'varchar', 50);
		DaoMap::setStringType('path', 'varchar', 255);
		DaoMap::setManyToMany('transactions', 'Transaction', DaoMap::RIGHT_SIDE, 'xt', true);
		DaoMap::setManyToMany('propertys', 'Property', DaoMap::RIGHT_SIDE, 'xp', true);
		
		DaoMap::createUniqueIndex('assetKey');
		
		DaoMap::commit();
	}
}