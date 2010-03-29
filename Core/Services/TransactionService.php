<?php
class TransactionService extends BaseService
{
	public function __construct()
	{
		parent::__construct("Transaction");
	}
	
	public function spendMoney(AccountEntry $fromAccount,AccountEntry $toAccount,$value,$comments="")
	{
		$comments = addslashes($comments);
		$accountService = new AccountEntryService();
		
		if(!is_numeric($value))
			throw new Exception("Invalid value to spend!");
			
		if($fromAccount->getId()==$toAccount->getId())
			throw new Exception("Can't make transaction between the same account!");
			
		if($fromAccount->getValue()<$value && $fromAccount->getRoot()->getId()==1)
			throw new Exception("You don't have enough money on this account to spend!");
		
		$transation = new Transaction();
		$transation->setFrom($fromAccount);
		$transation->setTo($toAccount);
		$transation->setValue($value);
		$transation->setComments($comments);
		$transation->setCreatedBy(System::getUser());
		$transation->setUpdatedBy(System::getUser());
		$this->save($transation);
		
		$fromAccount->setValue($fromAccount->getValue()- $value);
		$fromAccount->setUpdatedBy(System::getUser());
		$accountService->save($fromAccount);
		
		$toAccount->setValue($toAccount->getValue()+ $value);
		$toAccount->setUpdatedBy(System::getUser());
		$accountService->save($toAccount);
	}
	
	public function earnMoney(AccountEntry $fromAccount,AccountEntry $toAccount,$value,$comments="")
	{
		$comments = addslashes($comments);
		$accountService = new AccountEntryService();
		
		if(!is_numeric($value))
			throw new Exception("Invalid value to earn!");
			
		$transation = new Transaction();
		$transation->setFrom($fromAccount);
		$transation->setTo($toAccount);
		$transation->setValue($value);
		$transation->setComments($comments);
		$transation->setCreatedBy(System::getUser());
		$transation->setUpdatedBy(System::getUser());
		$this->save($transation);
		
		$fromAccount->setValue($fromAccount->getValue()+ $value);
		$fromAccount->setUpdatedBy(System::getUser());
		$accountService->save($fromAccount);
		
		$toAccount->setValue($toAccount->getValue()+ $value);
		$toAccount->setUpdatedBy(System::getUser());
		$accountService->save($toAccount);
	}
	
	
	public function getSumOfExpenseYear($year)
	{
		$qry = "select sum(t.value) as sum 
				from Transaction t 
				left join AccountEntry acc on (acc.id = t.toId)
				where t.active = 1
				and acc.rootId = 4 
				and YEAR(t.created)='$year'";
		$sql = new SqlStatement();
		$sql->setDoResults(true);
		
		$sql->setSQL($qry);
		
		$dao = new Dao();
		$dao->execute($sql);
		$results = $sql->getResultSet();
		
		return $results[0]["sum"];
	}
	
	public function getSumOfExpenseMonth($year,$month)
	{
		$qry = "select sum(t.value) as sum 
				from Transaction t 
				left join AccountEntry acc on (acc.id = t.toId)
				where t.active = 1
				and acc.rootId = 4 
				and MONTH(t.created)='$month' and YEAR(t.created)='$year'";
		$sql = new SqlStatement();
		$sql->setDoResults(true);
		
		$sql->setSQL($qry);
		
		$dao = new Dao();
		$dao->execute($sql);
		$results = $sql->getResultSet();
		
		return $results[0]["sum"];
	}
	
	public function getSumOfExpenseWeek($year,$week)
	{
		$qry = "select sum(t.value) as sum 
				from Transaction t 
				left join AccountEntry acc on (acc.id = t.toId)
				where t.active = 1
				and acc.rootId = 4 
				and  WEEK(t.created)='$week' and YEAR(t.created)='$year'";
		$sql = new SqlStatement();
		$sql->setDoResults(true);
		
		$sql->setSQL($qry);
		
		$dao = new Dao();
		$dao->execute($sql);
		$results = $sql->getResultSet();
		
		return $results[0]["sum"];
	}
	
	public function getSumOfExpenseDay($year,$month,$day)
	{
		$qry = "select sum(t.value) as sum 
				from Transaction t 
				left join AccountEntry acc on (acc.id = t.toId)
				where t.active = 1
				and acc.rootId = 4 
				and  MONTH(t.created)=$month and YEAR(t.created)='$year' and DAYOFMONTH(t.created)='$day'";
		$sql = new SqlStatement();
		$sql->setDoResults(true);
		
		$sql->setSQL($qry);
		
		$dao = new Dao();
		$dao->execute($sql);
		$results = $sql->getResultSet();
		
		return $results[0]["sum"];
	}
}
?>