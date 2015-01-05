<?php
/**
 * The Lost & Profit Panel
 * 
 * @package    Web
 * @subpackage Controls
 * @author     lhe<helin16@gmail.com>
 */
class ProfitPanel extends TTemplateControl  
{
    /**
     * AccountEntryService
     *
     * @var AccountEntryService
     */
    private $_accountService;
    /**
     * TransactionService
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
        $this->_transService = new TransactionService();
        $this->_accountService = new AccountEntryService();
    }
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
	    $this->getPage()->getClientScript()->registerStyleSheetFile('ProfitPanelCss', $this->publishAsset(__CLASS__ . '.css'));
	    $this->getPage()->getClientScript()->registerScriptFile('ProfitPanelJs', $this->publishAsset(__CLASS__ . '.js'));
	}
	/**
	 * Event: ajax call to get all the RecentTrans
	 *
	 * @param TCallback          $sender The event sender
	 * @param TCallbackParameter $param  The event params
	 *
	 * @throws Exception
	 */
	public function getInfo($sender, $param)
	{
	    $results = $errors = array();
	    try
	    {
	        $excludeIncomePos = trim($param->CallbackParameter->excludeIncomePos);
	        $excludeExpensePos = trim($param->CallbackParameter->excludeExpensePos);
	        $results['accIds'] = $this->_getAccIds($excludeIncomePos, $excludeExpensePos);
	        
	        // day
	        $results['day'] = array();
	        $today = new UDate("now");
	        $start = $today->getDateTime()->format('Y-m-d 00:00:00');
	        $today->modify("+1 day");
	        $end = $today->getDateTime()->format('Y-m-d 00:00:00');
	        $results['day']['range']['start'] = trim($start);
	         $results['day']['range']['end'] = trim($end);
	        $results['day']['income'] = $this->_transService->getSumOfExpenseBetweenDates(trim($start), trim($end), AccountEntry::TYPE_INCOME, $excludeIncomePos);
	        $results['day']['expense'] = $this->_transService->getSumOfExpenseBetweenDates(trim($start), trim($end), AccountEntry::TYPE_EXPENSE, $excludeExpensePos);
	        
	        // week
	        $results['week'] = array();
	        $today = new UDate("now");
	        $weekDay = $today->getDateTime()->format('N');
	        if($weekDay>=4)
	            $today->modify("-".($weekDay-4)." day");
	        else
	            $today->modify("-".(3+$weekDay)." day");
	        $start = $today->getDateTime()->format("Y-m-d 00:00:00");
	        $today->modify("+1 week");
	        $end = $today->getDateTime()->format("Y-m-d 00:00:00");
	        $results['week']['range']['start'] = trim($start);
	        $results['week']['range']['end'] = trim($end);
	        $results['week']['income'] = $this->_transService->getSumOfExpenseBetweenDates(trim($start), trim($end), AccountEntry::TYPE_INCOME, $excludeIncomePos);
	        $results['week']['expense'] = $this->_transService->getSumOfExpenseBetweenDates(trim($start), trim($end), AccountEntry::TYPE_EXPENSE, $excludeExpensePos);
	        
	        // month
	        $results['month'] = array();
	        $today = new UDate("now");
	        $start = $today->getDateTime()->format("Y-m-01 00:00:00");
	        $today->modify("+1 month");
	        $end = $today->getDateTime()->format("Y-m-01 00:00:00");
	        $results['month']['range']['start'] = trim($start);
	        $results['month']['range']['end'] = trim($end);
	        $results['month']['income'] = $this->_transService->getSumOfExpenseBetweenDates(trim($start), trim($end), AccountEntry::TYPE_INCOME, $excludeIncomePos);
	        $results['month']['expense'] = $this->_transService->getSumOfExpenseBetweenDates(trim($start), trim($end), AccountEntry::TYPE_EXPENSE, $excludeExpensePos);
	        
	        // year
	        $results['year'] = array();
	        $today = new UDate("now");
	        $midYearDate = new UDate($today->getDateTime()->format("Y-07-01 00:00:00"));
	        $start = new UDate(trim($midYearDate));
	        $end = new UDate(trim($midYearDate));
	        //if we passed 1st of July
	        if($today->afterOrEqualTo($midYearDate))
	        {
	            $end->modify('+1 year');
	            $end->modify('-1 second');
	        }
	        else //if we are in the first half year
	        {
	            $end->modify('-1 second');
	            $start->modify('-1 year');
	        }
	        $results['year']['range']['start'] = trim($start);
	        $results['year']['range']['end'] = trim($end);
	        $results['year']['income'] = $this->_transService->getSumOfExpenseBetweenDates(trim($start), trim($end), AccountEntry::TYPE_INCOME, $excludeIncomePos);
	        $results['year']['expense'] = $this->_transService->getSumOfExpenseBetweenDates(trim($start), trim($end), AccountEntry::TYPE_EXPENSE, $excludeExpensePos);
	        
	        // all
	        $results['all'] = array();
	        $today = new UDate("now");
	        $start = $today->getDateTime()->format("1791-01-01 00:00:00");
	        $today->modify("+1 year");
	        $end = $today->getDateTime()->format("9999-01-01 00:00:00");
	        $results['all']['range']['start'] = trim($start);
	        $results['all']['range']['end'] = trim($end);
	        $results['all']['income'] = $this->_transService->getSumOfExpenseBetweenDates(trim($start), trim($end), AccountEntry::TYPE_INCOME, $excludeIncomePos);
	        $results['all']['expense'] = $this->_transService->getSumOfExpenseBetweenDates(trim($start), trim($end), AccountEntry::TYPE_EXPENSE, $excludeExpensePos);
	    }
	    catch(Exception $e)
	    {
	        $errors[] = $e->getMessage();
	    }
	    $param->ResponseData = Core::getJson($results, $errors);
	    return $this;
	}
	/**
	 * Getting the Incomea and Expense account Ids
	 * 
	 * return array
	 */
	private function _getAccIds($excludeIncomePos = '', $excludeExpensePos = '')
	{
	    $accountIds = array();
	    $sql = "select id, rootId 
	    	from accountentry 
	    	where active = 1 and rootId in (" . AccountEntry::TYPE_INCOME . ", " . AccountEntry::TYPE_EXPENSE . ") " 
	        . (($excludeIncomePos !== '') ? " and accountNumber not like '{$excludeIncomePos}%'" : "")
	        . (($excludeExpensePos !== '') ? " and accountNumber not like '{$excludeExpensePos}%'" : "");
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