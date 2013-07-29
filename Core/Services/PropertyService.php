<?php
/**
 * Property Service
 * 
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 */
class PropertyService extends BaseService 
{
    /**
     * constructor
     */
	public function __construct()
	{
	    parent::__construct("Property");
	}
	/**
	 * adding an asset to a transaction
	 *
	 * @param Property $trans The Property
	 * @param Asset    $asset The asset
	 *
	 * @return Transaction
	 */
	public function addAsset(Property $trans, Asset $asset)
	{
	    EntityDao::getInstance('Property')->saveManyToManyJoin($asset, $trans);
	    return $this->get($trans->getId());
	}
	/**
	 * removing an asset to a Property
	 *
	 * @param Property $trans The Property
	 * @param Asset    $asset The asset
	 *
	 * @return Transaction
	 */
	public function removeAsset(Property $trans, Asset $asset)
	{
	    EntityDao::getInstance('Property')->deleteManyToManyJoin($asset, $trans);
	    return $this->get($trans->getId());
	}
}
