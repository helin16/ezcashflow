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
		$accountService = new accountentryService();
		$sql ="select 
					(ceil((length(accountNumber)-1)/4)) len,
					acc.id,
					acc.name,
					acc.accountNumber,
					acc.value 
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
		$entity->setValue(trim($this->DataList->getEditItem()->accountValue->Text));
		$accountService->save($entity);
	    
		$this->DataList->EditItemIndex = -1;
		$this->showAccounts();   
		$this->setInfoMsg("Account Saved Successfully!"); 	
    } 
}
?>