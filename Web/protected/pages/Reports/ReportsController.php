<?php
/**
 * This is the Transactions page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class ReportsController extends PageAbstract 
{
    /**
     * construct
     */
	public function __construct()
	{
		parent::__construct();
		$this->menuItemName='reports';
	}
	/**
	 * (non-PHPdoc)
	 * @see TPage::onPreInit()
	 */
	public function onPreInit($param)
	{
	    $td = new TDatePicker();
	    $this->getClientScript()->registerPradoScript('datepicker');
	    $tdpUrl = $this->getClientScript()->getPradoScriptAssetUrl() . '/' . TDatePicker::SCRIPT_PATH . '/' . $td->getCalendarStyle() . '.css';
	    $this->getClientScript()->registerStyleSheetFile($tdpUrl, $tdpUrl);
	    parent::onPreInit($param);
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		if(!$this->IsPostBack)
		{
		    $reportVars = array();
		    if(isset($this->Request['transid']))
		        $reportVars['transId'] = trim($this->Request['transid']);
		    else if(isset($this->Request['reportVars']))
		        $reportVars = json_decode($this->Request['reportVars'], true);
		    $this->getClientScript()->registerEndScript('pageJs', $this->_getJs($reportVars));
		}
	}
	/**
	 * generate the javascript
	 * 
	 * @param array $reportVars The variables passed in via URL
	 * 
	 * @return string
	 */
	private function _getJs($reportVars)
	{
	    $fromAccountIds = isset($reportVars['fromAccountIds']) ? $reportVars['fromAccountIds'] : array();
	    $toAccountIds = isset($reportVars['toAccountIds']) ? $reportVars['toAccountIds'] : array();
	    $fromDate = isset($reportVars['fromDate']) ? trim($reportVars['fromDate']) : '';
	    $toDate = isset($reportVars['toDate']) ? trim($reportVars['toDate']) : '';
	    $transId = isset($reportVars['transId']) ? trim($reportVars['transId']) : '';
	    $js = " pageJs.initSearchPane('$transId', '$fromDate', '$toDate', [" . implode(',', $fromAccountIds) . "], [" . implode(',', $toAccountIds) . "]);";
        $js .= "pageJs.initChosen('.chosen-select');";
        $js .= "pageJs.initialDatePicker('input.searchdate');";
        if(count($reportVars) > 0)
        {
            if($transId === '')
                $js .= "pageJs.getSearchPanel().down('.hidesearchbtn').click();";
            else 
                $js .= "pageJs.getSearchPanel().hide();";
            $js .= "pageJs.search();";
        }
        return $js;
	}
	/**
	 * Event: ajax call to get all the accounts
	 * 
	 * @param TCallback          $sender The event sender
	 * @param TCallbackParameter $param  The event params
	 * 
	 * @throws Exception
	 */
	public function searchTrans($sender, $param)
	{
	    $results = $errors = array();
	    try 
	    {
	        $transId = trim($param->CallbackParameter->search->transId);
	        if($transId !== '') 
	        {
	            if(!($trans = BaseService::getInstance('TransactionService')->get($transId)) instanceof Transaction)
	                throw new Exception('Invalid transaction ID:' . $transId);
	            $results['total'] = 1;
	            $results['trans'] = array($trans->getJsonArray());
	        }
	        else
	        {
    	        $date_start = trim($param->CallbackParameter->search->date_start);
        	    $date_end = trim($param->CallbackParameter->search->date_end);
        	    $fromaccIds = $param->CallbackParameter->search->fromacc;
        	    $toaccIds = $param->CallbackParameter->search->toacc;
        	    $pageNo = trim($param->CallbackParameter->pagination->pageNo);
        	    $pageSize = trim($param->CallbackParameter->pagination->pageSize);
        	    
        	    if($date_start === '' && $date_end === '' && count($fromaccIds) === 0 && count($toaccIds) === 0)
        	        throw new Exception('We need at least one search criteria to search!');
        	    
        	    $where = 'active = 1';
        	    if($date_start !== '')
        	        $where .= " AND created >= '$date_start'";
        	    if($date_end !== '')
        	        $where .= " AND created <= '$date_end'";
        	    if(count($fromaccIds) !== 0)
        	        $where .= " AND fromId in(" . implode(', ', $fromaccIds) . ")";
        	    if(count($toaccIds) !== 0)
        	        $where .= " AND toId in(" . implode(', ', $toaccIds) . ")";
        	    $trans = BaseService::getInstance('TransactionService')->findByCriteria($where, array(), true, $pageNo, $pageSize, array("created" => "desc"));
        	    $stats = BaseService::getInstance('TransactionService')->getPageStats();
        	    $results['total'] = $stats['totalRows'];
        	    $results['trans'] = array();
        	    foreach($trans as $tran)
        	    {
        	        $results['trans'][] = $tran->getJsonArray();
        	    }
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
	 * Event: ajax call to delete a transaction
	 * 
	 * @param TCallback          $sender The event sender
	 * @param TCallbackParameter $param  The event params
	 * 
	 * @throws Exception
	 */
	public function deleteTrans($sender, $param)
	{
	    $results = $errors = array();
	    try 
	    {
	        if(!isset($param->CallbackParameter->transId) || ($transId = trim($param->CallbackParameter->transId)) === '')
    	        throw new Exception('System Error: transId not found!');
    	    
    	    $trans = BaseService::getInstance('TransactionService')->get($transId);
    	    $trans->setActive(false);
    	    BaseService::getInstance('TransactionService')->save($trans);
    	    $results = $trans->getJsonArray();
	    }
	    catch(Exception $e)
	    {
	        $errors[] = $e->getMessage();
	    }
	    $param->ResponseData = $this->_getJson($results, $errors);
	    return $this;
	}
	/**
	 * Event: ajax call to save a transaction
	 * 
	 * @param TCallback          $sender The event sender
	 * @param TCallbackParameter $param  The event params
	 * 
	 * @throws Exception
	 */
	public function editTrans($sender, $param)
	{
	    $results = $errors = array();
	    try 
	    {
	        Dao::beginTransaction();
	        if(!isset($param->CallbackParameter->transId) || ($transId = trim($param->CallbackParameter->transId)) === '' || !($trans = BaseService::getInstance('TransactionService')->get($transId)) instanceof Transaction)
    	        throw new Exception('System Error: trans Id not found!');
	        if(!isset($param->CallbackParameter->date) || ($date = trim($param->CallbackParameter->date)) === '')
    	        throw new Exception('System Error: date can not be empty!');
	        if(!isset($param->CallbackParameter->toacc) || ($toaccId = trim($param->CallbackParameter->toacc)) === '' || !($toAcc = BaseService::getInstance('AccountEntryService')->get($toaccId)) instanceof AccountEntry)
    	        throw new Exception('System Error: to account can not be empty!');
	        if(!isset($param->CallbackParameter->value) || ($value = trim($param->CallbackParameter->value)) === '')
    	        throw new Exception('System Error: value can not be empty!');
	        $fromAcc = ((isset($param->CallbackParameter->fromacc) && ($fromaccId = trim($param->CallbackParameter->fromacc)) !== '') && (($fromAcc = BaseService::getInstance('AccountEntryService')->get($fromaccId)) instanceof AccountEntry)) ? $fromAcc : null;
	        $comments = (isset($param->CallbackParameter->comments) && ($comments = trim($param->CallbackParameter->comments)) !== '') ? $comments : '';
	        $assets = (isset($param->CallbackParameter->assets) && count($assets = $param->CallbackParameter->assets) !== 0) ? $assets : array();
	        $attachments = (isset($param->CallbackParameter->attachments) && count($attachments = $param->CallbackParameter->attachments) !== 0) ? json_decode(json_encode($attachments), true) : array();
	        
    	    $trans->setCreated($date);
    	    $trans->setFrom($fromAcc);
    	    $trans->setTo($toAcc);
    	    $trans->setValue($value);
    	    $trans->setComments($comments);
    	    BaseService::getInstance('TransactionService')->save($trans);
    	    
    	    //update existing assets
    	    foreach($assets as $assetkey => $active)
    	    {
    	        if(($active !== false) || !($asset = BaseService::getInstance('AssetService')->getAssetByKey($assetkey)) instanceof Asset)
    	            continue;
        	    BaseService::getInstance('TransactionService')->removeAsset($trans, $asset);
    	        $asset->setActive($active);
    	        BaseService::getInstance('AssetService')->save($asset);
    	    }
    	    //creating new assets
    	    foreach($attachments as $fileKey => $asset)
    	    {
    	        $filePath = trim($asset['tmpDir']) . DIRECTORY_SEPARATOR . trim($asset['filepath']);
                if(is_file($filePath))
                    $trans = BaseService::getInstance('TransactionService')->addAsset($trans, BaseService::getInstance('AssetService')->registerFile(AssetType::ID_DOC, $filePath, trim($asset['name'])));
    	    }
    	    $results = $trans->getJsonArray();
	        Dao::commitTransaction();
	    }
	    catch(Exception $e)
	    {
	        Dao::rollbackTransaction();
	        $errors[] = $e->getMessage();
	    }
	    $param->ResponseData = $this->_getJson($results, $errors);
	    return $this;
	}
}
?>