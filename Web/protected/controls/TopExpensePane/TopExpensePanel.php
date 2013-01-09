<?php

class TopExpensePanel extends TPanel  
{
	public $noOfItems=10;
	public $excludingAccountIds="91";
	
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
		$notLike = array();
		foreach(Dao::getResultsNative("select accountnumber from accountentry where active = 1 and id in (".implode(",",$excludingAccountIds).")") as $row)
		{
			$notLike[] = $row['accountnumber'];
		}
		$now = new UDate();
		$sql ="select distinct acc.id,
					acc.name,
					acc.budget,
					(
						select round(((if(sum(tt.value) is null,0,sum(tt.value))/((TIMESTAMPDIFF(second,acc.created,'$now')))) * 3600 * 24 *365 /12),2) 
						from transaction tt 
						inner join accountentry acc1 on (acc1.active = 1 and tt.toId=acc1.id)
						where tt.active = 1
						and acc1.accountNumber like concat(acc.accountNumber,'%')
					) `sum`
				from accountentry acc
				where acc.rootId=4
				and acc.budget!=0
				".(count($notLike)>0 ? "and (acc.accountNumber not like '".(implode("%' AND acc.accountNumber not like '",$notLike))."%')" : "")."
				group by acc.id
				having (`sum`-acc.budget)>0
				order by round((`sum`-acc.budget)) desc
				limit {$this->noOfItems}";
		$accounts = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		
		$html="<table>";
			//show all names
			$names = array();
			$html.="<tr style='background:#555555;color:#dddddd;'>";
				$html .="<td width='120px'>Name</td>";
				$html .="<td width='80px'>Spend</td>";
				$html .="<td width='80px'>Budget</td>";
				$html .="<td width='80px'>Over</td>";
			$html.="</tr>";
			$totalOverBudget = 0;
			$row=0;
			foreach($accounts as $account)
			{
				$html.="<tr ".( $row++ % 2!=0 ? "style='background:#cccccc;'" : "").">";
					$overBudget =$account["sum"]-$account["budget"];
					$html .="<td>{$account["name"]}</td>";
					$html .="<td>".$this->makeURLToReport("$".$account["sum"],array(),array($account["id"]),"","")."</td>";
					$html .="<td>\${$account["budget"]}</td>";
					$html .="<td>\$$overBudget</td>";
					$totalOverBudget +=$overBudget;
				$html.="</tr>";
			}
			$html.="<tr style='background:#555555;color:#dddddd;'>";
				$html .="<td>Total</td>";
				$html .="<td>&nbsp;</td>";
				$html .="<td>&nbsp;</td>";
				$html .="<td>$totalOverBudget</td>";
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