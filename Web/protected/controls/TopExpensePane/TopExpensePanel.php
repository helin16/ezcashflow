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
		
		$sql ="select distinct acc.id,
					acc.name,
					acc.budget,
					(
						select if(sum(tt.value) is null,0,sum(tt.value)) 
						from transaction tt 
						inner join accountentry acc1 on (acc1.active = 1 and tt.toId=acc1.id)
						where tt.active = 1
						and acc1.accountNumber like concat(acc.accountNumber,'%')
					) `sum`
				from accountentry acc
				where acc.rootId=4
				and acc.budget!=0
				and acc.id not in (".implode(",",$excludingAccountIds).")
				order by round((`sum`-acc.budget)) desc
				limit {$this->noOfItems}";
		$accounts = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		
		$html="<table width='100%'>";
			//show all names
			$names = array();
			$html.="<tr style='background:black;color:white;'>";
			foreach($accounts as $account)
			{
				$html .="<td>{$account["name"]}<br /><i style='font-size:10px'>$ {$account["budget"]} ~ $".($account["sum"]-$account["budget"])."</i></td>";
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