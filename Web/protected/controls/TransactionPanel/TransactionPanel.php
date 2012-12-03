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
     * From Account Root Ids, i.e: 1, 2
     * 
     * @var string
     */
	public $fromAccountRootIds = "";
    /**
     * To Account Root Ids, i.e: 1, 2
     * 
     * @var string
     */
	public $toAccountRootIds = "";
	/**
	 * Transaction type: transfer(spend) or income(this is a double entry)
	 * 
	 * @var string
	 */
	public $transType = '';
	/**
	 * Tye Transfer type of transaction
	 * 
	 * @var string
	 */
	const TransType_Transfer="trans";
	/**
	 * Tye Income type of transaction
	 * 
	 * @var string
	 */
	const TransType_Income="income";
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
		$this->transType = TransactionPanel::TransType_Transfer;
		$this->_transService = new TransactionService();
		$this->_accountService = new AccountEntryService();
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
	    $this->getPage()->getClientScript()->registerStyleSheetFile('TransPanelCss', $this->publishAsset(__CLASS__ . '.css'));
		if(!$this->Page->IsPostBack || $param == "reload")
		{
			$this->_loadAccounts($this->fromAccounts,$this->fromAccountRootIds)
			    ->_loadAccounts($this->toAccounts,$this->toAccountRootIds);
		}
	}
	/**
	 * getter fromAccountRootIds
	 *
	 * @return fromAccountRootIds
	 */
	public function getFromAccountRootIds()
	{
		return $this->fromAccountRootIds;
	}
	/**
	 * setter fromAccountRootIds
	 *
	 * @var fromAccountRootIds
	 */
	public function setFromAccountRootIds($fromAccountRootIds)
	{
		$this->fromAccountRootIds = $fromAccountRootIds;
	}
	/**
	 * getter toAccountRootIds
	 *
	 * @return toAccountRootIds
	 */
	public function getToAccountRootIds()
	{
		return $this->toAccountRootIds;
	}
	/**
	 * setter toAccountRootIds
	 *
	 * @var toAccountRootIds
	 */
	public function setToAccountRootIds($toAccountRootIds)
	{
		$this->toAccountRootIds = $toAccountRootIds;
	}
	/**
	 * getter transType
	 *
	 * @return transType
	 */
	public function getTransType()
	{
		return $this->transType;
	}
	/**
	 * setter transType
	 *
	 * @var transType
	 */
	public function setTransType($transType)
	{
		$this->transType = $transType;
	}
	/**
	 * Loading all the accounts
	 * 
	 * @param TDropDownList $list           The list we will bind our account to 
	 * @param string        $accountRootIds The account root ids
	 * 
	 * @return TransactionPanel
	 */
	private function _loadAccounts(TDropDownList &$list,$accountRootIds)
	{
		$accountRootIds = trim($accountRootIds);
		if($accountRootIds=="") return;
		$accountRootIds = explode(",",$accountRootIds);
		if(count($accountRootIds)==0) return;
		
		$array = array();
		foreach($accountRootIds as $rootId)
		{
			$result = $this->_accountService->findByCriteria("rootId in ($rootId) and id not in (".implode(",",$accountRootIds).")");
			foreach($result as $a)
			{
				if(count($a->getChildren())==0)
					$array[strtolower(str_replace(" ","",$a->getBreadCrumbs()))] = $a;
			}
		}
		krsort($array);
		$array = array_reverse($array);
		$list->DataSource = $array;
		$list->DataBind();
		return $this;
	}
	/**
	 * Event: saving the transaction
	 * 
	 * @param TButton $sender The event sender
	 * @param Mixe    $param  The event params
	 * 
	 * @return TransactionPanel 
	 */
	public function save($sender, $param)
	{
		$msg="";
		$value = str_replace(",","",trim($this->transValue->Text));
		if(preg_match("/^(\d{1,3}(\,\d{3})*|(\d+))(\.\d{1,2})?$/", $value))
		{
			if($this->transType==TransactionPanel::TransType_Transfer)
				$this->_transferMoney("transferMoney");
			else if($this->transType==TransactionPanel::TransType_Income)
				$this->_transferMoney("earnMoney","Earned Successfully!");
		}
		else
			$msg ="digits only!";
		$this->valueMsg->Text = $msg;
		return $this;
	}
	/**
	 * Reloading this panel
	 * 
	 * @return TransactionPanel
	 */
	public function reload()
	{
		$this->_loadAccounts($this->fromAccounts,$this->fromAccountRootIds)
		    ->_loadAccounts($this->toAccounts,$this->toAccountRootIds);
		$this->transValue->Text="";
		$this->description->Text="";
		return $this;
	}
	/**
	 * Recording the transaction
	 * 
	 * @param string $function   The function for the TransactionService
	 * @param string $successMsg The msg that will be displayed after saving the transaction successfully
	 * 
	 * @return TransactionPanel
	 */
	private function _transferMoney($function = "transferMoney", $successMsg = "Spend Successfully!")
	{
		$this->fromAccountsMsg->Text="";
		$this->toAccountsMsg->Text="";
		$this->errorMsg->Text="";
		$this->infoMsg->Text="";
		$fromAccountId = $this->fromAccounts->getSelectedValue();
		$fromAccount = $this->_accountService->get($fromAccountId);
		if(!$fromAccount instanceof AccountEntry)
		{
			$this->fromAccountsMsg->Text ="Invalid from account!";
			return $this;
		}
		$toAccountId = $this->toAccounts->getSelectedValue();
		$toAccount = $this->_accountService->get($toAccountId);
		if(!$toAccount instanceof AccountEntry)
		{
			$this->toAccountsMsg->Text="Invalid to account!";
			return $this;
		}
		$value = str_replace(",","",trim($this->transValue->Text));
		$description=trim($this->description->Text);
		try
		{
			$this->_transService->$function($fromAccount,$toAccount,$value,$description);
		}
		catch(Exception $ex)
		{
			$this->errorMsg->Text= $ex->getMessage();
			return $this;
		}
		$this->Page->reload();
		$this->infoMsg->Text = $successMsg;
		$this->fromAccounts->focus();
		return $this;
	}
}

?>