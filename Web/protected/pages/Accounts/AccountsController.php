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
     * constructor
     */
	public function __construct()
	{
		parent::__construct();
		$this->menuItemName='accounts';
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

		$this->populateEditItem($itemIndex);
    }
    
    private function populateEditItem($itemIndex)
    {
    	$this->DataList->SelectedItemIndex = -1;
		$this->DataList->EditItemIndex = $itemIndex;
		$this->showAccounts();
		
		$this->DataList->getEditItem()->accountName->focus();
		$accountService = new AccountEntryService();
		$array = $this->DataList->getEditItem()->getData();
		$this->DataList->getEditItem()->subAccountno->Text = $accountService->getNextAccountNo($accountService->get($array["id"]));
		
		$array = array();
		$moveAccounts = array();
		foreach($accountService->findAll() as $a)
		{
			$array[strtolower(str_replace(" ","",$a->getBreadCrumbs()))] = $a;
			if($a->getSum()==0)
				$moveAccounts[strtolower(str_replace(" ","",$a->getBreadCrumbs()))] = $a;
		}
		krsort($array);
		$array = array_reverse($array);
		krsort($moveAccounts);
		$moveAccounts = array_reverse($moveAccounts);
		$this->DataList->getEditItem()->moveTransToAccountList->DataSource = $array;
		$this->DataList->getEditItem()->moveTransToAccountList->DataBind();
		$this->DataList->getEditItem()->moveToAccountList->DataSource = $moveAccounts;
		$this->DataList->getEditItem()->moveToAccountList->DataBind();
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
    	$editIndex = $this->DataList->EditItemIndex;
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
    	
    	$this->setInfoMsg("new Account(".$subAccount->getLongshot().") added successfully!");
    	$this->populateEditItem($editIndex);
    }
    
    public function moveAllTransactions($sender,$param)
    {
    	$editIndex = $this->DataList->EditItemIndex;
    	$fromAccountId = trim($param->CommandParameter);
    	$toAccountId = trim($this->DataList->getEditItem()->moveTransToAccountList->getSelectedValue());
    	
    	$userAccountId = Core::getUser()->getId();
    	$sql="update transaction set toId = $toAccountId,updatedById =$userAccountId where toId = $fromAccountId and active = 1";
    	Dao::execSql($sql);
    	$sql="update transaction set fromId = $toAccountId,updatedById =$userAccountId where fromId = $fromAccountId and active = 1";
    	Dao::execSql($sql);
    	
    	$this->setInfoMsg("All transactions have been updated from '$fromAccountId' to '$toAccountId'!");
    	$this->populateEditItem($editIndex);
    }
    
    public function moveAccount($sender,$param)
    {
    	$editIndex = $this->DataList->EditItemIndex;
    	$fromAccountId = trim($param->CommandParameter);
    	$toAccountId = trim($this->DataList->getEditItem()->moveToAccountList->getSelectedValue());
    	
    	$accountService = new AccountEntryService();
    	$fromAccount = $accountService->get($fromAccountId);
    	$fromAccountNo = $fromAccount->getAccountNumber();
    	$toAccount = $accountService->get($toAccountId);
    	$toAccountNo = $toAccount->getAccountNumber();
    	
    	$newFromAccountNo = $accountService->getNextAccountNo($toAccount);
    	$fromAccount->setAccountNumber($newFromAccountNo);
    	$fromAccount->setParent($toAccount);
    	$fromAccount->setRoot($toAccount->getRoot());
    	$accountService->save($fromAccount);
    	
    	$userAccountId = Core::getUser()->getId();
    	$sql="update accountentry set rootId = ".$toAccount->getRoot()->getId().",accountNumber = REPLACE(accountNumber,'$fromAccountNo','$newFromAccountNo'),updatedById =$userAccountId where accountNumber like '$fromAccountNo%' and active = 1";
    	Dao::execSql($sql);
    	
    	$this->setInfoMsg("Account '".$fromAccount->getLongshot()."' have been updated to be under '".$toAccount->getLongshot()."'!");
    	$this->populateEditItem($editIndex);
    }
    
    public function showArrow($accountNo,$direction)
    {
    	$accountService = new AccountEntryService();
    	$account = $accountService->getAccountFromAccountNo($accountNo);
    	if(!$account instanceof AccountEntry)
    		return false;
    		
    	if(count($account->getChildren())>0)
    		return false;
    		
    	if(strlen($accountNo)==1)
    		return false;
    	$nextNo = ($direction=="up" ? ($accountNo - 1) : ($accountNo + 1 ));
    	$sql="select id from accountentry where accountNumber = '$nextNo' and active = 1 limit 1";
    	$result = Dao::getResultsNative($sql);
    	return count($result)>0;
    }
    
    public function showDelete($accountId)
    {
    	$accountService = new AccountEntryService();
    	$account = $accountService->get($accountId);
    	if(!$account instanceof AccountEntry)
    		return false;
    	return count($account->getChildren())==0;
    }
    
    public function deleteAccount($sender,$param)
    {
    	$this->setErrorMsg("");
    	$this->setInfoMsg("");
    	$accountService = new AccountEntryService();
    	$account = $accountService->get(trim($param->CommandParameter));
    	if(!$account instanceof AccountEntry)
    	{
    		$this->setErrorMsg("Invalid account!");
    		return;
    	}
    	
    	$account->setActive(false);
    	$accountService->save($account);
    	$this->setInfoMsg("Account '".$account->getLongshot()."' deleted!");
    	$this->showAccounts();   
    }
    
    public function movePosition($sender,$param)
    {
    	$this->setErrorMsg("");
    	$this->setInfoMsg("");
    	list($accountId,$direction) = explode(":",trim($param->CommandParameter));
    	
    	$accountService = new AccountEntryService();
    	$account = $accountService->get($accountId);
    	$parentAccountNo = $account->getParent()->getAccountNumber();
    	$accountNo = $account->getAccountNumber();
    	$accountNoNext = (strtolower(trim($direction))=="up" ? ( $accountNo - 1) : ($accountNo + 1));
    	
    	$anotherAccount = $accountService->getAccountFromAccountNo($accountNoNext);
    	if(!$anotherAccount instanceof AccountEntry)
    	{
    		$this->setErrorMsg("Invalid account!");
    		return;
    	}
    	$account->setAccountNumber($parentAccountNo."000");
    	$accountService->save($account);
    	
    	$anotherAccount->setAccountNumber($accountNo);
    	$accountService->save($anotherAccount);
    	
    	$account->setAccountNumber($accountNoNext);
    	$accountService->save($account);
    	
    	$this->setInfoMsg("Account '".$account->getLongshot()."' re-ordered!");
    	$this->showAccounts();   
    }
}
?>