<?php
class ReportsController extends EshopPage 
{
	public $totalValue;
	public function __construct()
	{
		parent::__construct();
		$this->menuItemName='reports';
		$this->totalValue=0;
	}
	
	public function onLoad($param)
	{
		if(!$this->IsPostBack)
		{
			$this->bindAccounts($this->fromAccount);
			$this->bindAccounts($this->toAccount);
			
			$reportVars = isset($this->Request['reportVars']) ? unserialize($this->Request['reportVars']) : array();
			if(count($reportVars)>0)
			{
				$this->fromDate->Text = $reportVars["fromDate"];
				$this->toDate->Text = $reportVars["toDate"];
				
				$this->fromAccount->setSelectedValues($reportVars["fromAccountIds"]);
				$this->toAccount->setSelectedValues($reportVars["toAccountIds"]);
				$this->search(null,null);
			}
		}
	}
	
	public function bindAccounts($list)
	{
		$accountService = new AccountEntryService();
		$array = array();
		foreach($accountService->findAll() as $a)
		{
			$array[$a->getLongshot()] = $a;
		}
		ksort($array);
		$list->DataSource = $array;
		$list->DataBind();
	}
	
	public function search($sender,$param)
	{
		$this->setErrorMsg("");
		$this->setInfoMsg("");
		
		$transactions = $this->getData();
		if($transactions===null)
		{
			$this->DataList->DataSource = array();
			$this->DataList->DataBind();
			$this->setErrorMsg("Nothing to Search");
			return;
		}
		
		if($sender instanceof TButton  && $sender->getId()=="searchBtn")
		{
			$this->DataList->SelectedItemIndex = -1;
			$this->DataList->EditItemIndex = -1;
		}
		
		$this->DataList->DataSource = $transactions;
		$this->DataList->DataBind();
	}
	
	private function getData()
	{
		$where = "1";
		$fromDate = trim($this->fromDate->Text);
			$where .= ($fromDate=="" ? "" : " AND created >='$fromDate'");
		$toDate = trim($this->toDate->Text);
			$where .= ($toDate=="" ? "" : " AND created <'$toDate'");
		
		$fromAccountIds = $this->fromAccount->getSelectedValues();
			$where .= (count($fromAccountIds)==0 ? "" : " AND fromId in (".implode(",",$fromAccountIds).")");
		$toAccountIds = $this->toAccount->getSelectedValues();
			$where .= (count($toAccountIds)==0 ? "" : " AND toId in (".implode(",",$toAccountIds).")");
		
		if($where=="1")	return null;
			
		$transactionService = new TransactionService();
		$transactions = $transactionService->findByCriteria("$where",array(),null,30,array("Transaction.created"=>"desc"));
		return $transactions;
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
    
	public function addTotalValue($value)
    {
    	$this->totalValue +=$value;
    	return $value;
    }
    
    public function toExcel($sender,$param)
    {
    	$transactions = $this->getData();
    	$html ="<table border='1'>";
	    	$html .="<tr style='background:black;color:white;'>";
		    	$html .="<td>Date Time</td>";
		    	$html .="<td>From</td>";
		    	$html .="<td>To</td>";
		    	$html .="<td>Value</td>";
		    	$html .="<td>Comments</td>";
	    	$html .="</tr>";
	    	foreach($transactions as $trans)
	    	{
	    		$created = $trans->getCreated();
	    		$from = $trans->getFrom();
	    		$to = $trans->getTo();
	    		$value = $trans->getValue();
	    		$comments = $trans->getComments();
	    		$html .="<tr >";
			    	$html .="<td>$created</td>";
			    	$html .="<td>$from</td>";
			    	$html .="<td>$to</td>";
			    	$html .="<td>$value</td>";
			    	$html .="<td>$comments</td>";
	    		$html .="</tr>";
	    	}
    	$html .="</table>";
    	
    	$export_file = "cashflow_report.xls";
	    ob_end_clean();
	    ini_set('zlib.output_compression','Off');
	   
	    header('Pragma: public');
	    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");                  // Date in the past   
	    header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
	    header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1
	    header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
	    header ("Pragma: no-cache");
	    header("Expires: 0");
	    header('Content-Transfer-Encoding: none');
	    header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera
	    header("Content-type: application/x-msexcel");                    // This should work for the rest
	    header('Content-Disposition: attachment; filename="'.basename($export_file).'"'); 
	    echo $html;
	    die;
    }
}
?>