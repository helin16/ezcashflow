<?php
/**
 * EOFY Service
 * 
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 */
class EOFYService extends BaseService 
{
    /**
     * constructor
     */
	public function __construct()
	{
	    parent::__construct("EOFY");
	}
	/**
	 * adding an asset to a eofy
	 *
	 * @param EOFY  $eofy  The transaction
	 * @param Asset $asset The asset
	 *
	 * @return Transaction
	 */
	public function addAsset(EOFY $eofy, Asset $asset)
	{
	    EntityDao::getInstance($this->_entityName)->saveManyToManyJoin($asset, $eofy);
	    return $this->get($trans->getId());
	}
	/**
	 * removing an asset to a eofy
	 *
	 * @param EOFY  $eofy  The transaction
	 * @param Asset $asset The asset
	 *
	 * @return Transaction
	 */
	public function removeAsset(EOFY $eofy, Asset $asset)
	{
	    EntityDao::getInstance($this->_entityName)->deleteManyToManyJoin($asset, $eofy);
	    return $this->get($trans->getId());
	}
}
