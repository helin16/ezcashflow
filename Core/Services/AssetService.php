<?php
/**
 * Asset Service
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 */
class AssetService extends BaseService
{
    /**
     * The asset type service 
     * @var AssetType
     */
    private $_typeDao;
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("Asset");
    }
    public function registerFile($filePath, $fileName, $fileType)
    {
        $key = md5(Core::getUser() . $fileName . microtime());
        $asset = new Asset();
        $asset->setAssetKey($key)
            ->setAssetType()
        return $key;
    }
}