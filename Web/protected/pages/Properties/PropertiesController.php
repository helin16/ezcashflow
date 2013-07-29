<?php
/**
 * This is the Properties page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
require_once dirname(__FILE__) . '/../../controls/OverdueRental/OverdueRentalPanel.php';
class PropertiesController extends PageAbstract 
{
    /**
     * constructor
     */
	public function __construct()
	{
		parent::__construct();
		$this->menuItemName = 'properties';
		
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		parent::onLoad($param);
		if(!$this->IsPostBack)
	    {
	    	$this->_loadOverdueRental();
	    	$this->_loadLastRentalTrans();
	    }
	}
	/**
	 * loadding the overdue rental
	 *
	 * @return PropertiesController
	 */
	private function _loadOverdueRental()
	{
		$this->_addRightPanel('<div class="box"><div class="title">Overdue Rentals</div><div class="content" id="overdueRentals">');
		$this->_addRightPanel(new OverdueRentalPanel());
		$this->_addRightPanel('</div></div>');
		return $this;
	}
	/**
	 * loadding the last rental transaction
	 *
	 * @return PropertiesController
	 */
	private function _loadLastRentalTrans()
	{
		$html = '<div class="box"><div class="title">Last Rentals</div><div class="content overduerentalwrapper" id="lastRentals">';
		foreach(BaseService::getInstance('PropertyService')->findAll() as $property)
		{
			$lastestTrans = $property->getLastesIncomeTrans(null, 1, 1);
			//if we can't find the lastest transaction in the last month, then it's an overdue
			if(count($lastestTrans) > 0)
			{
				$overdue = array('property' => $property->getJsonArray(), 'lastTrans' => $lastestTrans[0]->getJsonArray());
				$html .= str_replace('#{transId}', $overdue['lastTrans']['id'], 
						str_replace('#{address.full}', $overdue['property']['address']['full'], 
							str_replace('#{lastDate}', $overdue['lastTrans']['updated'],
								str_replace('#{lastAmount}', '$' . number_format($overdue['lastTrans']['value'], 2),
									OverdueRentalPanel::ITEM_TEMPLATE
								)
							)
						)
					);
			}
		}
		$html .= '</div></div>';
		$this->_addRightPanel($html);
		return $this;
	}
	/**
	 * Event: ajax call to get all the properties
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
    		$properties = BaseService::getInstance('PropertyService')->findAll(true, $pagination->pageNumber, $pagination->pageSize);
    		$stats = BaseService::getInstance('PropertyService')->getPageStats();
    		$results['total'] = $stats['totalRows'];
    		$results['properties'] = array();
    		foreach($properties as $property)
    		{
    		    $results['properties'][] = $this->_formatProperty($property);
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
	 * Prepare the property json
	 * 
	 * @param Property $property The property object to format
	 * 
	 * @return array
	 */
	private function _formatProperty(Property $property)
	{
	    $now = new UDate();
	    $pArray = $property->getJsonArray();
	    $currentFYmidYr = new UDate($now->format('Y-07-01 00:00:00'));
	    $pArray['currentFY'] = $this->_getFY($property, $currentFYmidYr, $now);
	    $currentFYmidYr->modify('-1 year');
	    $now->modify('-1 year');
	    $pArray['lastFY'] = $this->_getFY($property, $currentFYmidYr, $now);
	    return $pArray;
	}
	/**
	 * Event: ajax call to get all the accounts
	 * 
	 * @param TCallback          $sender The event sender
	 * @param TCallbackParameter $param  The event params
	 * 
	 * @throws Exception
	 */
	public function getAccEntries($sender, $param)
	{
	    $results = $errors = array();
	    try 
	    {
    		$results['accounts'] = array();
    		foreach(BaseService::getInstance('AccountEntryService')->findAll() as $account) 
		        $results['accounts'][$account->getRoot()->getId()][$account->getId()] = $account->getJsonArray();
    		$results['states'] = array();
    		foreach(BaseService::getInstance('StateService')->findAll() as $state)
		        $results['states'][$state->getId()] = $state->getJsonArray();
	    }
	    catch(Exception $e)
	    {
	        $errors[] = $e->getMessage();
	    }
	    $param->ResponseData = $this->_getJson($results, $errors);
	    return $this;
	}
	/**
	 * Event: ajax call to save the property
	 * 
	 * @param TCallback          $sender The event sender
	 * @param TCallbackParameter $param  The event params
	 * 
	 * @throws Exception
	 */
	public function saveProperty($sender, $param)
	{
	    $results = $errors = array();
	    try 
	    {
	        $transStarted = false;
	        try {Dao::beginTransaction(); } catch(Exception $e) {$transStarted = true;}
	        $params = json_decode(json_encode($param->CallbackParameter), true);
	        if(!isset($params['id']) || !($property = BaseService::getInstance('PropertyService')->get($params['id'])) instanceof Property)
	            throw new Exception('Invalid property ID: ' . $params['id']);
            $property->setBoughtValue(trim($params['boughtValue']));
            $property->setComments(trim($params['comments']));
            $property->setSetupAcc(BaseService::getInstance('AccountEntryService')->get(trim($params['setupAcc'])));
            $property->setIncomeAcc(BaseService::getInstance('AccountEntryService')->get(trim($params['incomeAcc'])));
            $property->setOutgoingAcc(BaseService::getInstance('AccountEntryService')->get(trim($params['outgoingAcc'])));
            
            $address = (($address = $property->getAddress()) instanceof Address) ? $address : new Address();
            $address->setLine1(trim($params['address']['line1']));
            $address->setSuburb(trim($params['address']['suburb']));
            $address->setPostCode(trim($params['address']['postcode']));
            $address->setState(BaseService::getInstance('StateService')->get(trim($params['address']['stateId'])));
            BaseService::getInstance('AddressService')->save($address);
            $property->setAddress($address);
            
            BaseService::getInstance('PropertyService')->save($property);
            //update existing assets
            $assets = (isset($params['assets']['assets']) && count($assets = $params['assets']['assets']) !== 0) ? $assets : array();
            foreach($assets as $assetkey => $active)
            {
                if(($active !== false) || !($asset = BaseService::getInstance('AssetService')->getAssetByKey($assetkey)) instanceof Asset)
                continue;
                BaseService::getInstance('PropertyService')->removeAsset($property, $asset);
                $asset->setActive($active);
                BaseService::getInstance('AssetService')->save($asset);
            }
            //creating new assets
            var_dump($params['assets']);
            var_dump($params['assets']['attachments']);
            $attachments = (isset($params['assets']['attachments']) && count($attachments = $params['assets']['attachments']) !== 0) ? $attachments : array();
            foreach($attachments as $fileKey => $asset)
            {
                $filePath = trim($asset['tmpDir']) . DIRECTORY_SEPARATOR . trim($asset['filepath']);
                if(is_file($filePath))
                $trans = BaseService::getInstance('PropertyService')->addAsset($property, BaseService::getInstance('AssetService')->registerFile(AssetType::ID_DOC, $filePath, trim($asset['name'])));
            }
            
            $results = $this->_formatProperty($property);
            
	       
	       if($transStarted === false)
	           Dao::commitTransaction();
	    }
	    catch(Exception $e)
	    {
	        if($transStarted === false)
	            Dao::rollbackTransaction();
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
	            'income' => BaseService::getInstance('TransactionService')->getSumOfExpenseBetweenDates(trim($start), trim($end), AccountEntry::TYPE_INCOME, '', $property->getIncomeAcc()),
	            'outgoing' => BaseService::getInstance('TransactionService')->getSumOfExpenseBetweenDates(trim($start), trim($end), AccountEntry::TYPE_EXPENSE, '', $property->getOutgoingAcc()),
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