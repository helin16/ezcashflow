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
	
	public function earnMoney(AccountEntry $toAccount,AccountEntry $fromAccount,$value,$comments="")
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

	public function getSumOfExpenseBetweenDates($startDate,$endDate,$accountTypeId=4)
	{
		$qry = "select sum(t.value) as sum 
				from Transaction t 
				left join AccountEntry acc on (acc.id = t.toId)
				where t.active = 1
				and acc.rootId = $accountTypeId 
				and t.created >='$startDate' and t.created<'$endDate'";

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