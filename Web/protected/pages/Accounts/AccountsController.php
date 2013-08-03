<?php
/**
 * This is the accounts page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class AccountsController extends PageAbstract 
{
    /**
     * constructor
     */
	public function __construct()
	{
		parent::__construct();
		$this->menuItemName = 'accounts';
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
				
			$accounts = $this->_accService->get($rootId)->getChildren(true, false, null, DaoQuery::DEFAUTL_PAGE_SIZE, array('accountNumber' => 'asc'));
			foreach($accounts as $account)
			{
				if(!$account instanceof AccountEntry)
					continue;
				$results[$account->getId()] = $acc->getJsonArray();
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
    	    if(!($account = BaseService::getInstance('AccountEntryService')->get($param->CallbackParameter->accountId)) instanceof AccountEntry && !($parent = BaseService::getInstance('AccountEntryService')->get($param->CallbackParameter->parentId)) instanceof AccountEntry)
    	        throw new Exception('System Error: we need at least one of them: accountId or parentId!');
    	    if(($accountName = trim($param->CallbackParameter->name)) === '')
    	        throw new Exception('System Error: we need name for the account!');
    	    if(!is_numeric(($accountValue = trim($param->CallbackParameter->value))))
    	        throw new Exception('System Error: it is not numeric for the value!');
    	    if(!is_numeric(($accountBudget = trim($param->CallbackParameter->budget))))
    	        throw new Exception('System Error: it is not numeric for the budget!');
    	    $comments = trim($param->CallbackParameter->comments);
    	    
    	    if ($account instanceof AccountEntry)
    	        $account = BaseService::getInstance('AccountEntryService')->updateAccount($account, $account->getParent(), $accountName, $accountValue, $comments, $accountBudget);
    	    else
    	        $account = BaseService::getInstance('AccountEntryService')->createAccount($parent, $accountName, $accountValue, $comments, $accountBudget);
    	    $results = $account->getJsonArray();
	    }
	    catch(Exception $e)
	    {
	        $errors[] = $e->getMessage();
	    }
	    $param->ResponseData = $this->_getJson($results, $errors);
	    return $this;
	}
	/**
	 * Event: ajax call to m Account
	 * 
	 * @param TCallback          $sender The event sender
	 * @param TCallbackParameter $param  The event params
	 * 
	 * @throws Exception
	 */
	public function moveAccount($sender, $param)
	{
	    $results = $errors = array();
	    try 
	    {
    	    if(!($account = BaseService::getInstance('AccountEntryService')->get($param->CallbackParameter->accountId)) instanceof AccountEntry || !($parent = BaseService::getInstance('AccountEntryService')->get($param->CallbackParameter->parentId)) instanceof AccountEntry)
    	        throw new Exception('System Error: we need both them: accountId and parentId!');
    	    $account = BaseService::getInstance('AccountEntryService')->moveAccount($parent, $account);
    	    foreach($account->getRoot()->getChildren(true, false, null, DaoQuery::DEFAUTL_PAGE_SIZE, array('accountNumber' => 'asc')) as $acc)
    	    {
    	        $results[$acc->getId()] = $acc->getJsonArray();
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
	        Dao::beginTransaction();
    	    if(($accountId = trim($param->CallbackParameter->accountId)) === '' || !( $account = BaseService::getInstance('AccountEntryService')->get($accountId)) instanceof AccountEntry)
    	        throw new Exception('System Error: Invalid account id provided!');
    	    $account->setActive(false);
    	    $results = $account->getJsonArray();
    	    BaseService::getInstance('AccountEntryService')->save($account);
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