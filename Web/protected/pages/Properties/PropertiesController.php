<?php
/**
 * This is the Properties page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class PropertiesController extends PageAbstract 
{
    /**
     * Property service
     * 
     * @var PropertyService
     */
    private $_proService;
    /**
     * constructor
     */
	public function __construct()
	{
		parent::__construct();
		$this->menuItemName = 'properties';
		$this->_proService = new PropertyService();
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		if(!$this->IsPostBack)
		{
		}
	}
	/**
	 * Event: ajax call to get all the accounts
	 * 
	 * @param TCallback          $sender The event sender
	 * @param TCallbackParameter $param  The event params
	 * 
	 * @throws Exception
	 */
	public function getProperties($sender, $param)
	{
	    $results = $errors = array();
	    try 
	    {
    	    if(!isset($param->CallbackParameter->pagination))
    	        throw new Exception('Pagination not found!');
    	    
    	    $pagination = $param->CallbackParameter->pagination;
    		$properties = $this->_proService->findAll(true, $pagination->pageNumber, $pagination->pageSize);
    		foreach($properties as $property)
    		{
    		    if(!$property instanceof Property)
    		        continue;
    		    $results[] = $property->getJsonArray();
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