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
     * Asset Service
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
    /**
     * Event: ajax call to edit a asset type
     *
     * @param TCallback          $sender The event sender
     * @param TCallbackParameter $param  The event params
     *
     * @throws Exception
     */
    public function editAssetType($sender, $param)
    {
        $results = $errors = array();
	    try 
	    {
	        if(!isset($param->CallbackParameter->id) || ($id = trim($param->CallbackParameter->id)) === '')
    	        throw new Exception('System Error:Id not found!');
    	    $entity = $this->_assetService->getAllAssetType($id);
    	    if(!$entity instanceof AssetType)
    	        throw new Exception('System Error: Invalid id(=' . $id . ') provided!');
	        
	        if(!isset($param->CallbackParameter->name) || ($name = trim($param->CallbackParameter->name)) === '')
    	        throw new Exception('System Error: name can not be empty!');
	        if(!isset($param->CallbackParameter->path) || ($path = trim($param->CallbackParameter->path)) === '')
    	        throw new Exception('System Error: path can not be empty!');
    	    $results = $this->_assetService->saveAssetType($entity, $name, $path)->getJsonArray();
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