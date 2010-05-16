<?php

class TopExpensePanel extends TPanel  
{
	public $noOfItems=5;
	public $excludingAccountIds="32,33,34";
	public $startDate = "1790-01-01 00:00:00";
	public $endDate = "9999-12-31 23:59:59";
	
	/**
	 * getter noOfItems
	 *
	 * @return noOfItems
	 */
	public function getNoOfItems()
	{
		return $this->noOfItems;
	}
	
	/**
	 * setter noOfItems
	 *
	 * @var noOfItems
	 */
	public function setNoOfItems($noOfItems)
	{
		$this->noOfItems = $noOfItems;
	}
	
	/**
	 * getter excludingAccountIds
	 *
	 * @return excludingAccountIds
	 */
	public function getExcludingAccountIds()
	{
		return $this->excludingAccountIds;
	}
	
	/**
	 * setter excludingAccountIds
	 *
	 * @var excludingAccountIds
	 */
	public function setExcludingAccountIds($excludingAccountIds)
	{
		$this->excludingAccountIds = $excludingAccountIds;
	}
	
	public function renderEndTag($writer)
	{
		$html = $this->loadAccounts();
		$writer->write($html);
		parent::renderEndTag($writer);
	}
	
	public function loadAccounts()
	{
		$transactionService = new TransactionService();
		
		$excludingAccountIds=explode(",",$this->excludingAccountIds);
		
		$accounts = $transactionService->getTopExpenses(4,$this->noOfItems,$this->startDate,$this->endDate,$excludingAccountIds);
		
		$html="<table width='100%'>";
			//show all names
			$names = array();
			$html.="<tr style='background:black;color:white;'>";
			foreach($accounts as $account)
			{
				$html .="<td>{$account["name"]}</td>";
			}
			$html.="</tr>";
			
			$html.="<tr>";
			foreach($accounts as $account)
			{
				$html .="<td>".$this->makeURLToReport("$ ".$account["sum"],array(),array($account["id"]),"","")."</td>";
			}
			$html.="</tr>";
		$html.="</table>";
		
		return $html;
	}
	
	public function makeURLToReport($value,$fromAccountIds,$toAccountIds,$fromDate,$toDate)
	{
		$vars = array(
					"fromAccountIds"=>$fromAccountIds,
					"toAccountIds"=>$toAccountIds,
					"fromDate"=>$fromDate,
					"toDate"=>$toDate
				);
		$serial = serialize($vars);
		return "<a href='/reports/$serial'> $value</a>";
	}
}

?>