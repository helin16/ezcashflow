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
    		    $results[] = $this->_jsonAccountEntry($account);;
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
	 * formatting the account entry for the json
	 * 
	 * @param AccountEntry $account The account entry that we are try to format for
	 * 
	 * @return multitype:number NULL Ambigous <name, string> Ambigous <value, string> Ambigous <budget, string> Ambigous <comments, string> Ambigous <accountNumber, string>
	 */
	private function _jsonAccountEntry(AccountEntry $account)
	{
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
	    $acc['gotChildren'] = count($this->_accService->getChildrenAccounts($account)) !== 0;
	    $parent = $account->getParent();
	    $acc['parent'] = ($parent instanceof AccountEntry ? $this->_jsonAccountEntry($parent) : array());
	    return $acc;
	    
	}
	/**
	 * Event: ajax call to save Account
	 * 
	 * @param TCallback          $sender The event sender
	 * @param TCallbackParameter $param  The event params
	 * 
	 * @throws Exception
	 */
	public function saveAccount($sender, $param)
	{
	    $results = $errors = array();
	    try 
	    {
    	    if(($accountId = trim($param->CallbackParameter->accountId)) === '' && ($parentId = trim($param->CallbackParameter->parentId)) === '')
    	        throw new Exception('System Error: we need at least one of them: accountId or parentId!');
    	    if(($accountName = trim($param->CallbackParameter->name)) === '')
    	        throw new Exception('System Error: we need name for the account!');
    	    if(!is_numeric(($accountValue = trim($param->CallbackParameter->value))))
    	        throw new Exception('System Error: it is not numeric for the value!');
    	    if(!is_numeric(($accountBudget = trim($param->CallbackParameter->budget))))
    	        throw new Exception('System Error: it is not numeric for the budget!');
    	    
    	    $account = new AccountEntry();
    	    if ($accountId !== '')
    	        $account = $this->_accService->get($accountId);
    	    else
    	    {
    	        $parent = $this->_accService->get($parentId);
    	        $account->setParent($parent);
    	        $account->setRoot($parent->getRoot());
    	        $accountNumber = $this->_accService->getNextAccountNo($parent);
    	        $account->setAccountNumber($accountNumber);
    	    }
    	    $account->setName($accountName);
    	    $account->setValue($accountValue);
    	    $account->setBudget($accountBudget);
    	    $account->setComments(trim($param->CallbackParameter->comments));
    	    $this->_accService->save($account);
    	    $results = $this->_jsonAccountEntry($account);
	    }
	    catch(Exception $e)
	    {
	        $errors[] = $e->getMessage();
	    }
	    $param->ResponseData = $this->_getJson($results, $errors);
	    return $this;
	}
	/**
	 * Event: ajax call to delete Account
	 * 
	 * @param TCallback          $sender The event sender
	 * @param TCallbackParameter $param  The event params
	 * 
	 * @throws Exception
	 */
	public function delAccount($sender, $param)
	{
	    $results = $errors = array();
	    try 
	    {
    	    if(($accountId = trim($param->CallbackParameter->accountId)) === '' || !( $account = $this->_accService->get($accountId)) instanceof AccountEntry)
    	        throw new Exception('System Error: Invalid account id provided!');
    	    $account->setActive(false);
    	    $this->_accService->save($account);
    	    $results = $this->_jsonAccountEntry($account);
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