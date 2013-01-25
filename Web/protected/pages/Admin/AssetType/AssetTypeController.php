<?php
/**
 * The admin page - AssetType
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class AssetTypeController extends PageAbstract
{
    /**
     * Enter description here ...
     * 
     * @var AssetService
     */
    private $_assetService;
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_assetService = new AssetService();
    }
    /**
     * (non-PHPdoc)
     * @see PageAbstract::onLoad()
     */
    public function onLoad($param)
    {
    }
    /**
     * Event: ajax call to get all asset type
     *
     * @param TCallback          $sender The event sender
     * @param TCallbackParameter $param  The event params
     *
     * @throws Exception
     */
    public function getAssetTypes($sender, $param)
    {
        $results = $errors = array();
        try
        {
            foreach($this->_assetService->getAllAssetTypes() as $at)
            {
                $results[] = $at->getJsonArray();
            }
        }
        catch(Exception $e)
        {
            $errors[] = $e->getMessage();
        }
        $param->ResponseData = $this->_getJson($results, $errors);
        return $this;
    }
}
?>