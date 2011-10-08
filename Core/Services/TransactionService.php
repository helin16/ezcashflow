<?php
class TransactionService extends BaseService
{
	public function __construct()
	{
		parent::__construct("Transaction");
	}
	
	public function transferMoney(AccountEntry $fromAccount,AccountEntry $toAccount,$value,$comments="")
	{
		$comments = htmlentities($comments);
		$accountService = new AccountEntryService();
		
		if(!is_numeric($value))
			throw new Exception("Invalid value to spend!");
			
		if($fromAccount->getId()==$toAccount->getId())
			throw new Exception("Can't make transaction between the same account!");
			
		//if($fromAccount->getValue()<$value && $fromAccount->getRoot()->getId()==1)
			//throw new Exception("You don't have enough money on this account to spend!");
		
		$transation = new Transaction();
		$transation->setFrom($fromAccount);
		$transation->setTo($toAccount);
		$transation->setValue($value);
		$transation->setComments($comments);
		$this->save($transation);
	}
	
	public function earnMoney(AccountEntry $toAccount,AccountEntry $fromAccount,$value,$comments="")
	{
		$comments = htmlentities($comments);
		$accountService = new AccountEntryService();
		
		if(!is_numeric($value))
			throw new Exception("Invalid value to earn!");
			
		$transation = new Transaction();
		$transation->setFrom(null);
		$transation->setTo($fromAccount);
		$transation->setValue($value);
		$transation->setComments($comments);
		$this->save($transation);

		$transation1 = new Transaction();
		$transation1->setFrom(null);
		$transation1->setTo($toAccount);
		$transation1->setValue($value);
		$transation1->setComments($comments);
		$this->save($transation1);
	}

	public function getSumOfExpenseBetweenDates($startDate,$endDate,$accountTypeId=4,$excludePosition='')
	{
		$qry = "select sum(t.value) as sum 
				from transaction t 
				left join accountentry acc on (acc.id = t.toId)
				where t.active = 1
				and acc.rootId = $accountTypeId 
				".($excludePosition=='' ? '' : " and acc.accountNumber not like '$excludePosition%'")."
				and t.created >='$startDate' and t.created<'$endDate'";

		$results = Dao::getResultsNative($qry,array(),PDO::FETCH_ASSOC);
		return $results[0]["sum"];
	}
	
	public function getTopExpenses($rootId=4,$noOfItems=4,$startDate='1790-01-01 00:00:00',$endDate="9999-12-31 23:59:59",$excludingIds=array())
	{
		$qry = "select sum(t.value) as sum,acc.name,acc.id
				from transaction t 
				left join accountentry acc on (acc.id = t.toId)
				where t.active = 1
				and acc.rootId = $rootId 
				and t.created >='$startDate' and t.created<'$endDate'
				".(count($excludingIds)==0 ? "" : " AND acc.id not in (".implode(",",$excludingIds).")")."
				group by acc.id
				order by sum desc
				limit $noOfItems";

		$results = Dao::getResultsNative($qry,array(),PDO::FETCH_ASSOC);
		return $results;
	}

}
?>