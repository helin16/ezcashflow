<?php
class ReportsController extends EshopPage 
{
	public function __construct()
	{
		parent::__construct();
		$this->menuItemName='reports';
	}
	
	public function onLoad($param)
	{
		if(!$this->IsPostBack)
		{
			$this->bindAccounts($this->fromAccount);
			$this->bindAccounts($this->toAccount);
		}
	}
	
	public function bindAccounts($list)
	{
		$sql ="
			select ac.id,concat(acr.name,' - ', ac.name, ' - $',
					(
						if(ac.value='',0,round(ac.value,2))
						+round((
							select if(sum(t.value) is null,0, sum(t.value))
							from transaction t 
							where (t.active =1 and t.toId=ac.id)
						),2)
						-round((
						select if(sum(t.value) is null,0, sum(t.value))
						from transaction t 
						where (t.active =1 and t.fromId=ac.id)
						),2) 
					)
					) as name
				
				from accountentry ac 
				inner join accountentry acr on (acr.id = ac.rootId and acr.active = 1)
				where ac.active = 1 
				and ((select if(count(acc.id)=0,1,0) from accountentry acc where acc.parentId = ac.id and acc.active = 1))=1
				order by ac.rootId asc, name asc
			";
		$result = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		$list->DataSource = $result;
		$list->DataBind();
	}
	
	public function search($sender,$param)
	{
		$this->setErrorMsg("");
		$this->setInfoMsg("");
		$where = "1";
		$fromDate = trim($this->fromDate->Text);
			$where .= ($fromDate=="" ? "" : " AND created >='$fromDate'");
		$toDate = trim($this->toDate->Text);
			$where .= ($toDate=="" ? "" : " AND created <='$toDate'");
		
		$fromAccountId = trim($this->fromAccount->getSelectedValue());
			$where .= ($fromAccountId=="" ? "" : " AND fromId ='$fromAccountId'");
		$toAccountId = trim($this->toAccount->getSelectedValue());
			$where .= ($toAccountId=="" ? "" : " AND toId ='$toAccountId'");
		
		if($where=="1")
		{
			$this->DataList->DataSource = array();
			$this->DataList->DataBind();
			$this->setErrorMsg("Nothing to Search");
			return;
		}
		$transactionService = new TransactionService();
		$transactions = $transactionService->findByCriteria("$where",array(),null,30,array("Transaction.created"=>"desc"));
		
		if($sender instanceof TButton  && $sender->getId()=="searchBtn")
		{
			$this->DataList->SelectedItemIndex = -1;
			$this->DataList->EditItemIndex = -1;
		}
		
		$this->DataList->DataSource = $transactions;
		$this->DataList->DataBind();
	}
	
	public function edit($sender,$param)
    {
    	$this->setErrorMsg("");
		$this->setInfoMsg("");
	    if($param != null)
			$itemIndex = $param->Item->ItemIndex;
		else
			$itemIndex = 0;

		$this->DataList->SelectedItemIndex = -1;
		$this->DataList->EditItemIndex = $itemIndex;
		
		$this->search(null,null);
		
		$editItem = $this->DataList->getEditItem();
		$this->bindAccounts($editItem->fromAccountList);
		$this->bindAccounts($editItem->toAccountList);
		$editItem->date->Text=$editItem->getData()->getCreated();
		$editItem->value->Text=$editItem->getData()->getValue();
		$editItem->comments->Text=$editItem->getData()->getComments();
		
		$from = $editItem->getData()->getFrom();
		$fromId = $from instanceof AccountEntry ? $from->getId() : "";
		$toId = $editItem->getData()->getTo()->getId();
		$editItem->fromAccountList->setSelectedValue($fromId);
		$editItem->toAccountList->setSelectedValue($toId);
		$this->DataList->getEditItem()->date->focus();

    }
    
	public function cancel($sender,$param)
    {
    	$this->setErrorMsg("");
		$this->setInfoMsg("");
		$this->DataList->EditItemIndex = -1;
		$this->search(null,null);  	
    }
    
    public function save($sender,$param)
    {
    	$this->setErrorMsg("");
		$this->setInfoMsg("");
		
    	$accountService = new AccountEntryService();
    	$transService= new TransactionService();
    	$params = $param->Item;
    	
		$trans = $transService->get($this->DataList->DataKeys[$params->ItemIndex]);
		$editItem = $this->DataList->getEditItem();
		$trans->setCreated(trim($editItem->date->Text));
		$trans->setValue(trim($editItem->value->Text));
		$trans->setComments(trim($editItem->comments->Text));
		$trans->setFrom($accountService->get(trim($editItem->fromAccountList->getSelectedValue())));
		$trans->setTo($accountService->get(trim($editItem->toAccountList->getSelectedValue())));
		$transService->save($trans);
	    
		$this->DataList->EditItemIndex = -1;
		$this->search(null,null);  	 
		$this->setInfoMsg("Transaction Saved Successfully!"); 	
    } 
}
?>