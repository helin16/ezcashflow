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
     * Transaction service
     * 
     * @var TransactionService
     */
    private $_transService;
    /**
     * constructor
     */
	public function __construct()
	{
		parent::__construct();
		$this->menuItemName = 'properties';
		$this->_proService = new PropertyService();
		$this->_transService = new TransactionService();
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
    		$stats = $this->_proService->getPageStats();
    		$results['total'] = $stats['totalRows'];
    		$results['properties'] = array();
    		foreach($properties as $property)
    		{
        		$now = new UDate();
    		    $pArray = $property->getJsonArray();
    		    $currentFYmidYr = new UDate($now->format('Y-07-01 00:00:00'));
    		    $pArray['currentFY'] = $this->_getFY($property, $currentFYmidYr, $now);
    		    $currentFYmidYr->modify('-1 year');
    		    $now->modify('-1 year');
    		    $pArray['lastFY'] = $this->_getFY($property, $currentFYmidYr, $now);
    		    $results['properties'][] = $pArray;
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
	 * Getting the current Financial year's data for a property
	 * 
	 * @param Property $property The property
	 * 
	 * @return array
	 */
	private function _getFY(Property $property, UDate $midYearDate, UDate $now)
	{
	    $start = new UDate(trim($midYearDate));
	    $end = new UDate(trim($midYearDate));
	    //if we passed 1st of July
	    if($now->afterOrEqualTo($midYearDate))
	    {
	        $end->modify('+1 year');
	        $end->modify('-1 second');
	    }
	    else //if we are in the first half year 
	    {
	        $end->modify('-1 second');
	        $start->modify('-1 year');
	    }
	    
	    $array = array(
	            'date' => array('from' => trim($start), 'to' => trim($end)),
	            'income' => $this->_transService->getSumOfExpenseBetweenDates(trim($start), trim($end), AccountEntry::TYPE_INCOME, '', $property->getIncomeAcc()),
	            'outgoing' => $this->_transService->getSumOfExpenseBetweenDates(trim($start), trim($end), AccountEntry::TYPE_EXPENSE, '', $property->getOutgoingAcc()),
	            'incomeAccIds' => array_map(create_function('$a', 'return $a->getId();'), $property->getIncomeAcc()->getChildren(true)),
	            'outgoingAccIds' => array_map(create_function('$a', 'return $a->getId();'), $property->getOutgoingAcc()->getChildren(true))
	    );
	    return $array;
	}
	/**
	 * Getting the Incomea and Expense account Ids
	 *
	 * return array
	 */
	private function _getAccIds()
	{
	    $accountIds = array();
	    $sql = "select id, rootId
	    from accountentry
	    where active = 1 and rootId in (" . AccountEntry::TYPE_INCOME . ", " . AccountEntry::TYPE_EXPENSE . ") ";
	    foreach(Dao::getResultsNative($sql) as $row)
	    {
	        if(!isset($accountIds[$row["rootId"]]))
	            $accountIds[$row["rootId"]] = array();
	        $accountIds[$row["rootId"]][] = $row["id"];
	    }
	    return $accountIds;
	}
}
?>