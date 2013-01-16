<?php
/**
 * The transaction panel
 * 
 * @package    Web
 * @subpackage Controls
 * @author     lhe
 *
 */
class TransactionPanel extends TTemplateControl  
{
    /**
     * The javascript that will run after saving
     * @var string
     */
    private $_postJs;
	/**
	 * AccountEntryService
	 * 
	 * @var AccountEntryService
	 */
	private $_accountService;
	/**
	 * TransactionService
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
	 * Getter for the postJs
	 * 
	 * @return string The javascript string
	 */
	public function getPostJs()
	{
	    return $this->postJs;
	}
	/**
	 * Setter for the postJs
	 * 
	 * @param string $postJs The post javascript after saving
	 * 
	 * @return TransactionPanel
	 */
	public function setPostJs($postJs)
	{
	    $this->postJs = $postJs;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
	    $this->getPage()->getClientScript()->registerStyleSheetFile('TransPanelCss', $this->publishAsset(__CLASS__ . '.css'));
	    $this->getPage()->getClientScript()->registerScriptFile('TransPanelJs', $this->publishAsset(__CLASS__ . '.js'));
	}
	/**
	 * Event: ajax call to get all the accounts
	 *
	 * @param TCallback          $sender The event sender
	 * @param TCallbackParameter $param  The event params
	 *
	 * @throws Exception
	 */
	public function saveTrans($sender, $param)
	{
	    $results = $errors = array();
	    try
	    {
	        if(!isset($param->CallbackParameter->fromIds) || count($fromIds = $param->CallbackParameter->fromIds) === 0)
	            throw new Exception('fromIds not found!');
	        if(!isset($param->CallbackParameter->toIds) || count($toIds = $param->CallbackParameter->toIds) === 0)
	            throw new Exception('toIds not found!');
	        if(!isset($param->CallbackParameter->fromAccId) || !($fromAccount = ($this->_accountService->get($param->CallbackParameter->fromAccId))) instanceof AccountEntry)
	            throw new Exception('fromAccId not found!');
	        if(!isset($param->CallbackParameter->toAccId) || !($toAccount = ($this->_accountService->get($param->CallbackParameter->toAccId))) instanceof AccountEntry)
	            throw new Exception('toAccId not found!');
	        if(!isset($param->CallbackParameter->value) || ($value = trim($param->CallbackParameter->value)) <= 0)
	            throw new Exception('value not found!');
	        if(!isset($param->CallbackParameter->comments))
	            throw new Exception('comments not found!');
	        $comments = trim($param->CallbackParameter->comments);
	        
	        if($fromAccount->getRoot()->getId() == AccountEntry::TYPE_INCOME)
    	        $transArray = $this->_transService->earnMoney($fromAccount, $toAccount, $value, $comments);
	        else
	            $transArray = array($this->_transService->transferMoney($fromAccount, $toAccount, $value, $comments));
	        
	        $results['trans'] = array();
	        foreach($transArray as $trans)
	            $results['trans'][] = $trans->getJsonArray();
	        $results = array_merge($results, $this->_getAccList($fromIds, $toIds));
	    }
	    catch(Exception $e)
	    {
	        $errors[] = $e->getMessage();
	    }
	    $param->ResponseData = Core::getJson($results, $errors);
	    return $this;
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
	        if(!isset($param->CallbackParameter->fromIds) || count($fromIds = $param->CallbackParameter->fromIds) === 0)
	            throw new Exception('fromIds not found!');
	        if(!isset($param->CallbackParameter->toIds) || count($toIds = $param->CallbackParameter->toIds) === 0)
	            throw new Exception('toIds not found!');
	        $results = $this->_getAccList($fromIds, $toIds);
	    }
	    catch(Exception $e)
	    {
	        $errors[] = $e->getMessage();
	    }
	    $param->ResponseData = Core::getJson($results, $errors);
	    return $this;
	}
	/**
	 * Getting the account list
	 * 
	 * @param array $fromIds The rootId for the from account
	 * @param array $toIds   The rootId fro the to account
	 * 
	 * @return Ambigous <multitype:multitype: , multitype:>
	 */
	private function _getAccList($fromIds, $toIds)
	{
	    $results = array();
	    $results['from'] = array();
	    foreach($this->_accountService->findByCriteria('id in (' . implode(', ', $fromIds) . ')') as $root)
	        $results['from'][$root->getName()] = $this->_getAccountList($root->getId());
	     
	    $results['to'] = array();
	    foreach($this->_accountService->findByCriteria('id in (' . implode(', ', $toIds) . ')') as $root)
	        $results['to'][$root->getName()] = $this->_getAccountList($root->getId());
	    return $results;
	}
	/**
	 * Getting the acocunt list
	 * 
	 * @param int $rootId The root Id
	 * 
	 * @return array
	 */
	private function _getAccountList($rootId)
	{
	    $accounts = array();
	    $results = $this->_accountService->findByCriteria('rootId = :rootId and id != :rootId', array('rootId' => $rootId), true, null, DaoQuery::DEFAUTL_PAGE_SIZE, array('rootId' => 'asc'));
	    foreach($results as $account)
	    {
	        if(!$account instanceof AccountEntry || count($account->getChildren()) > 0 )
	            continue;
	        $accArray = $account->getJsonArray(false);
	        $accounts[$accArray['breadCrumbs']['name']] = $accArray;
	    }
	    krsort($accounts);
	    $accounts = array_reverse($accounts);
	    return $accounts;
	}
	
	/**
	 * Loading all the accounts
	 * 
	 * @param TDropDownList $list           The list we will bind our account to 
	 * @param string        $accountRootIds The account root ids
	 * 
	 * @return TransactionPanel
	 */
	private function _loadAccounts(TDropDownList &$list, $accountRootIds)
	{
		if(($accountRootIds = trim($accountRootIds)) == "") return;
		$accountRootIds = explode(",", $accountRootIds);
		if(count($accountRootIds)==0) return;
		
		$array = array();
		foreach($accountRootIds as $rootId)
		{
			$result = $this->_accountService->findByCriteria("rootId in ($rootId) and id not in (" . implode(",", $accountRootIds) . ")");
			foreach($result as $a)
			{
				if(count($a->getChildren()) === 0 )
					$array[strtolower(str_replace(" ", "", $a->getBreadCrumbs()))] = $a;
			}
		}
		krsort($array);
		$array = array_reverse($array);
		$list->DataSource = $array;
		$list->DataBind();
		return $this;
	}
	/**
	 * File upload handler
	 * 
	 * @param TFileUploader $sender The file uploader
	 * @param Mixed         $param  The parameters
	 */
	public function fileUploaded($sender, $param)
	{
	    if($sender->HasFile)
	    {
	        $this->Result->Text = "
	        You just uploaded a file:
	        <br/>
	        Name: {$sender->FileName}
	        <br/>
	        Size: {$sender->FileSize}
	        <br/>
	        Type: {$sender->FileType}";
	    }
	    else {
	        $this->Result->Text= $sender->ErrorCode;
	    }
	}
}

?>