<?php
class AccountsController extends EshopPage 
{
	public function __construct()
	{
		parent::__construct();
		$this->menuItemName='accounts';
	}
	
	public function onLoad($param)
	{
		if(!$this->IsPostBack)
		{
			$this->rootId->Value=1;
			$this->showAccounts();
		}
	}
	
	public function showAccounts()
	{
		$rootId = trim($this->rootId->Value);
		$accountService = new AccountEntryService();
		$sql ="select 
					(ceil((length(accountNumber)-1)/4)) len,
					acc.id,
					acc.name,
					acc.accountNumber,
					acc.value,
					acc.budget
				from accountentry acc
				where acc.active = 1
				and acc.rootId = $rootId
				order by trim(acc.accountNumber)";
		$accounts = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		$this->DataList->DataSource = $accounts;
		$this->DataList->DataBind();
	}
	
	public function loadAccount($sender,$param)
	{
		$this->rootId->Value = trim($param->CommandParameter);
		$this->DataList->SelectedItemIndex = -1;
		$this->DataList->EditItemIndex = -1;
		$this->showAccounts();
	}
	
	public function getClassName($var)
	{
		if($var==$this->rootId->Value)
			return "item_active";
		else
			return "item";
	}
	
	public function getValue($accountId)
	{
		$accountService = new AccountEntryService();
		$sql="select count(distinct acc.id) `count` from accountentry acc where acc.active = 1 and acc.parentId = $accountId";
		$result = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		$count = $result[0]["count"];
		$msg = $accountService->getChildrenValueSum($accountService->get($accountId));
		if($count>0)
			$msg = "<b>$msg</b>";
		return $msg;
	}
	
	public function edit($sender,$param)
    {
	    if($param != null)
			$itemIndex = $param->Item->ItemIndex;
		else
			$itemIndex = 0;

		$this->DataList->SelectedItemIndex = -1;
		$this->DataList->EditItemIndex = $itemIndex;
		$this->showAccounts();
		
		$this->DataList->getEditItem()->accountName->focus();
		$accountService = new AccountEntryService();
		$array = $this->DataList->getEditItem()->getData();
		$this->DataList->getEditItem()->subAccountno->Text = $accountService->getNextAccountNo($accountService->get($array["id"]));
		
		$array = array();
		foreach($accountService->findAll() as $a)
		{
			$array[strtolower(str_replace(" ","",$a->getBreadCrumbs()))] = $a;
		}
		krsort($array);
		$array = array_reverse($array);
		$this->DataList->getEditItem()->moveTransToAccountList->DataSource = $array;
		$this->DataList->getEditItem()->moveTransToAccountList->DataBind();
    }
    
	public function cancel($sender,$param)
    {
		$this->DataList->EditItemIndex = -1;
		$this->showAccounts();    	
    }
    
    public function save($sender,$param)
    {
    	$accountService = new AccountEntryService();
    	$params = $param->Item;
    	
		$entity = $accountService->get($this->DataList->DataKeys[$params->ItemIndex]);
		$entity->setName(trim($this->DataList->getEditItem()->accountName->Text));
		$entity->setValue(str_replace(" ","",str_replace(",","",trim($this->DataList->getEditItem()->accountValue->Text))));
		$entity->setBudget(str_replace(" ","",str_replace(",","",trim($this->DataList->getEditItem()->accountBudget->Text))));
		$accountService->save($entity);
	    
		$this->DataList->EditItemIndex = -1;
		$this->showAccounts();   
		$this->setInfoMsg("Account Saved Successfully!"); 	
    } 
    
    public function makeURL($text,$fromAccountId="",$toAccountId="")
    {
    	if($fromAccountId=="" && $toAccountId=="")
    		return;
    		
    	$vars = array(
    				"fromAccountIds" => array($fromAccountId),
    				"toAccountIds" => array($toAccountId),
    				"fromDate" => "",
    				"toDate" => ""
    			);
    	$serial = serialize($vars);
		return "<a href='/reports/$serial'> $text</a>";
    }
    
    public function createNewAccount($sender,$param)
    {
    	$subAccountName = trim($this->DataList->getEditItem()->subAccountName->Text);
    	$subAccountNo = trim($this->DataList->getEditItem()->subAccountno->Text);
    	$subAccountValue = trim($this->DataList->getEditItem()->subAccountValue->Text);
    	$subAccountComments = trim($this->DataList->getEditItem()->subAccountComments->Text);
    	$subBudget = str_replace(" ","",str_replace(",","",trim($this->DataList->getEditItem()->subAccountBudget->Text)));
		$parentId = trim($param->CommandParameter);
    	
    	$accountService = new AccountEntryService();
		$parent = $accountService->get($parentId);
    	$subAccount = new AccountEntry();
    	$subAccount->setName($subAccountName);
    	$subAccount->setValue($subAccountValue);
    	$subAccount->setAccountNumber($subAccountNo);
    	$subAccount->setComments($subAccountComments);
    	$subAccount->setParent($parent);
    	$subAccount->setBudget($subBudget);
    	$subAccount->setRoot($parent->getRoot());
    	$accountService->save($subAccount);
    	
    	$this->setInfoMsg("new Account($subAccount) added successfully!");
    	$this->showAccounts();
    }
    
    public function moveAllTransactions($sender,$param)
    {
    	$fromAccountId = trim($param->CommandParameter);
    	$toAccountId = trim($this->DataList->getEditItem()->moveTransToAccountList->getSelectedValue());
    	
    	$userAccountId = Core::getUser()->getId();
    	$sql="update transaction set toId = $toAccountId,updatedById =$userAccountId where toId = $fromAccountId and active = 1";
    	Dao::execSql($sql);
    	$sql="update transaction set fromId = $toAccountId,updatedById =$userAccountId where fromId = $fromAccountId and active = 1";
    	Dao::execSql($sql);
    	
    	$this->setInfoMsg("All transactions have been updated from '$fromAccountId' to '$toAccountId'!");
    	$this->showAccounts();
    }
}
?>