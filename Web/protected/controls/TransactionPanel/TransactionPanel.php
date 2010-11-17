<?php

class TransactionPanel extends TTemplateControl  
{
	public $groupingText="";
	public $fromAccountRootIds = "";
	public $toAccountRootIds = "";
	public $pageFunc="";
	public $transType;
	
	const TransType_Transfer="trans";
	const TransType_Income="income";
	
	public function __construct()
	{
		parent::__construct();
		$this->transType = TransactionPanel::TransType_Transfer;
	}
	
	public function onLoad($param)
	{
		if(!$this->Page->IsPostBack || $param == "reload")
		{
			$this->loadAccounts($this->fromAccounts,$this->fromAccountRootIds);
			$this->loadAccounts($this->toAccounts,$this->toAccountRootIds);
		}
	}
	
	/**
	 * getter GroupingText
	 *
	 * @return GroupingText
	 */
	public function getGroupingText()
	{
		return $this->groupingText;
	}
	
	/**
	 * setter GroupingText
	 *
	 * @var GroupingText
	 */
	public function setGroupingText($GroupingText)
	{
		$this->groupingText = $GroupingText;
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
	 * getter pageFunc
	 *
	 * @return pageFunc
	 */
	public function getPageFunc()
	{
		return $this->pageFunc;
	}
	
	/**
	 * setter pageFunc
	 *
	 * @var pageFunc
	 */
	public function setPageFunc($pageFunc)
	{
		$this->pageFunc = $pageFunc;
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
	
	public function loadAccounts(TDropDownList &$list,$accountRootIds)
	{
		$accountRootIds = trim($accountRootIds);
		if($accountRootIds=="") return;
		$accountRootIds = explode(",",$accountRootIds);
		if(count($accountRootIds)==0) return;
		
		$array = array();
		$service = new BaseService("AccountEntry");
		foreach($accountRootIds as $rootId)
		{
			$result = $service->findByCriteria("rootId in ($rootId) and id not in (".implode(",",$accountRootIds).")");
			foreach($result as $a)
			{
				if(count($a->getChildren())==0)
					$array[strtolower(str_replace(" ","",$a->getBreadCrumbs()))] = $a;
			}
		}
		krsort($array);
		$list->DataSource = $array;
		$list->DataBind();
	}
	
	public function save($sender, $param)
	{
		$msg="";
		$value = str_replace(",","",trim($this->transValue->Text));
		if(preg_match("/^(\d{1,3}(\,\d{3})*|(\d+))(\.\d{1,2})?$/", $value))
		{
			$function =trim($this->pageFunc);
			if($function!="")
				$this->Page->$function($sender,$param);
			else if($this->transType==TransactionPanel::TransType_Transfer)
				$this->transferMoney("transferMoney");
			else if($this->transType==TransactionPanel::TransType_Income)
				$this->transferMoney("earnMoney","Earned Successfully!");
		}
		else
			$msg ="digits only!";
		
		$this->valueMsg->Text=$msg;
	}
	
	public function reload()
	{
		$this->loadAccounts($this->fromAccounts,$this->fromAccountRootIds);
		$this->loadAccounts($this->toAccounts,$this->toAccountRootIds);
		$this->transValue->Text="";
		$this->description->Text="";
	}
	
	public function transferMoney($function="transferMoney",$successMsg="Spend Successfully!")
	{
		$this->fromAccountsMsg->Text="";
		$this->toAccountsMsg->Text="";
		$this->errorMsg->Text="";
		$this->infoMsg->Text="";
		
		$accountService = new AccountEntryService();
		$fromAccountId = $this->fromAccounts->getSelectedValue();
		$fromAccount = $accountService->get($fromAccountId);
		if(!$fromAccount instanceof AccountEntry)
		{
			$this->fromAccountsMsg->Text ="Invalid from account!";
			return;
		}
		
		$toAccountId = $this->toAccounts->getSelectedValue();
		$toAccount = $accountService->get($toAccountId);
		if(!$toAccount instanceof AccountEntry)
		{
			$this->toAccountsMsg->Text="Invalid to account!";
			return;
		}
		
		$value = str_replace(",","",trim($this->transValue->Text));
		$description=trim($this->description->Text);
		
		try{
			$transService = new TransactionService();
			$transService->$function($fromAccount,$toAccount,$value,$description);
		}
		catch(Exception $ex)
		{
			$this->errorMsg->Text= $ex->getMessage();
			return;
		}
		
		$this->Page->reload();
		$this->infoMsg->Text = $successMsg;
		$this->fromAccounts->focus();
	}
}

?>