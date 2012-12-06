<?php
/**
 * This is the Transactions page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class ReportsController extends EshopPage 
{
    /**
     * Account service
     * 
     * @var AccountEntryService
     */
    private $_accService;
    /**
     * Transaction Service
     * 
     * @var TransactionService
     */
    private $_transService;
    /**
     * construct
     */
	public function __construct()
	{
		parent::__construct();
		$this->menuItemName='reports';
		$this->_accService = new AccountEntryService();
		$this->_transService = new TransactionService();
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
		    $this->seachpage->getControls()->add($this->_getSeachPanel());
		}
	}
	/**
	 * getting the search panel's html
	 * 
	 * @return string The html code
	 */
	private function _getSeachPanel() 
	{
	    $accounts = $this->_accService->findByCriteria("active = 1", true, null, 30, array("AccountEntry.accountNumber" => "asc"));
	    $html = '<div class="content-box searchPanel" ID="searchPanel">';
	        $html .= '<h3 class="box-title">Search Transactions</h3>';
	        $html .= '<div class="box-content">';
    	        $html .= '<div class="row">';
    	        $html .= '<span class="halfcut">';
        	        $html .= '<span class="label">From Date:</span>';
        	        $html .= '<span class="input"><input type="text" searchpane="date_start" class="searchdate"/></span>';
    	        $html .= '</span>';
    	        $html .= '<span class="halfcut">';
        	        $html .= '<span class="label">To Date:</span>';
        	        $html .= '<span class="input"><input type="text" searchpane="date_end" class="searchdate"/></span>';
    	        $html .= '</span>';
    	        $html .= '</div>';
    	        $html .= '<div class="row">';
        	        $html .= '<span class="label">From Account:</span>';
        	        $html .= '<span class="input accountselection">';
            	        $html .= '<select multiple="multiple" searchpane="fromacc" >';
            	            $html .= ($list = $this->_getAccountsList($accounts));
            	        $html .= '</select>';
        	        $html .= '</span>';
    	        $html .= '</div>';
    	        $html .= '<div class="row">';
        	        $html .= '<span class="label">To Account:</span>';
        	        $html .= '<span class="input accountselection">';
            	        $html .= '<select multiple="multiple" searchpane="toacc" >';
            	            $html .= $list;
            	        $html .= '</select>';
        	        $html .= '</span>';
    	        $html .= '</div>';
    	        $html .= '<div class="row">';
    	            $html .= '<input type="button" value="Search" class="submitBtn" onclick="pageJs.search(this); return false;"/>';
    	        $html .= '</div>';
	        $html .= '</div>';
	    $html .= '</div>';
	    return $html;
	}
	/**
	 * getting all the list of item for the select dropdown box
	 * 
	 * @param array $accounts The array of account entries
	 * 
	 * @return string The html
	 */
	private function _getAccountsList(array $accounts)
	{
	    $html = '';
	    foreach($accounts as $account)
	    {
	        $html .= "<option value='" . $account->getId() . "'>" . $account->getLongshot() . "</option>";
	    }
	    return $html;
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
    	    $trans = $this->_transService->findByCriteria($where, true, $pageNo, $pageSize, array("Transaction.created" => "desc"));
    	    $results['total'] = Dao::getTotalRows();
    	    $results['trans'] = array();
    	    foreach($trans as $tran)
    	    {
    	        $results['trans'][] = $this->_getJsonTrans($tran);
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
    	    
    	    $trans = $this->_transService->get($transId);
    	    $trans->setActive(false);
    	    $this->_transService->save($trans);
    	    $results = $this->_getJsonTrans($trans);
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
	        if(!isset($param->CallbackParameter->transId) || ($transId = trim($param->CallbackParameter->transId)) === '')
    	        throw new Exception('System Error: trans Id not found!');
	        
	        if(($date = trim($param->CallbackParameter->date)) === '')
    	        throw new Exception('System Error: date can not be empty!');
	        
	        if(($fromaccId = trim($param->CallbackParameter->fromacc)) === '' || !($fromAcc = $this->_accService->get($fromaccId)) instanceof AccountEntry)
    	        $fromAcc = null;
	        if(($toaccId = trim($param->CallbackParameter->toacc)) === '' || !($toAcc = $this->_accService->get($toaccId)) instanceof AccountEntry)
    	        throw new Exception('System Error: to account can not be empty!');
	        if(($value = trim($param->CallbackParameter->value)) === '')
    	        throw new Exception('System Error: value can not be empty!');
	        if(($comments = trim($param->CallbackParameter->comments)) === '')
    	        $comments = '';
    	    $trans = $this->_transService->get($transId);
    	    $trans->setCreated($date);
    	    $trans->setFrom($fromAcc);
    	    $trans->setTo($toAcc);
    	    $trans->setValue($value);
    	    $trans->setComments($comments);
    	    $results = $this->_getJsonTrans($trans);
	    }
	    catch(Exception $e)
	    {
	        $errors[] = $e->getMessage();
	    }
	    $param->ResponseData = $this->_getJson($results, $errors);
	    return $this;
	}
	/**
	 * Getting the array for a transaction for the json string
	 * 
	 * @param Transaction $trans The transaction
	 * 
	 * @return array
	 */
	private function _getJsonTrans(Transaction $trans)
	{
	    $tran = array();
	    $tran['id'] = $trans->getId();
	    $tran['value'] = $trans->getValue();
	    $tran['comments'] = $trans->getComments();
	    $tran['fromAcc'] = $this->_getJsonAccount($trans->getFrom());
	    $tran['toAcc'] = $this->_getJsonAccount($trans->getTo());
	    $tran['created'] = $trans->getCreated() . '';
	    return $tran;
	}
	/**
	 * Getting the array for an account for the json string
	 * 
	 * @param AccountEntry $account The account entry
	 * 
	 * @return array
	 */
	private function _getJsonAccount(AccountEntry $account = null)
	{
	    $acc = array();
	    if($account instanceof AccountEntry)
	    {
    	    $acc['id'] = $account->getId();
    	    $acc['name'] = $account->getBreadCrumbs();
	    }
	    return $acc;
	}
}
?>