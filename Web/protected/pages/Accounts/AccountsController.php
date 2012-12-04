<?php
/**
 * This is the accounts page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class AccountsController extends EshopPage 
{
    /**
     * Account service
     * 
     * @var AccountEntryService
     */
    private $_accService;
    /**
     * constructor
     */
	public function __construct()
	{
		parent::__construct();
		$this->menuItemName='accounts';
		$this->_accService = new AccountEntryService();
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
	public function getAccounts($sender, $param)
	{
	    $results = $errors = array();
	    try 
	    {
    	    if(!isset($param->CallbackParameter->rootId) || ($rootId = trim($param->CallbackParameter->rootId)) === '')
    	        throw new Exception('rootId not found!');
    	    
    		$accounts = $this->_accService->getChildrenAccounts($this->_accService->get($rootId), true, true);
    		foreach($accounts as $account)
    		{
    		    if(!$account instanceof AccountEntry)
    		        continue;
    		    $acc = array();
    		    $accountNo = $account->getAccountNumber();
    		    $acc['level'] = ceil((strlen($accountNo) - 1) / 4);
    		    $acc['id'] = $account->getId();
    		    $acc['name'] = $account->getName();
    		    $acc['accountNumber'] = $accountNo;
    		    $acc['value'] = $account->getValue();
    		    $acc['budget'] = $account->getBudget();
    		    $acc['comments'] = $account->getComments();
    		    $acc['sum'] = $this->_accService->getChildrenValueSum($account);
    		    $results[] = $acc;
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