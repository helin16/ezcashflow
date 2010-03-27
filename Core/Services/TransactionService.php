<?php
class TransactionService extends BaseService
{
	public function __construct()
	{
		parent::__construct("Transaction");
	}
	
	public function spendMoney(AccountEntry $fromAccount,AccountEntry $toAccount,$value)
	{
		$accountService = new AccountEntryService();
		
		if(!is_numeric($value))
			throw new Exception("Invalid value to spend!");
			
		if($fromAccount->getValue()<$value)
			throw new Exception("You don't have enough money on this account to spend!");
		
		$transation = new Transaction();
		$transation->setFrom($fromAccount);
		$transation->setTo($toAccount);
		$transation->setValue($value);
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
	
	public function earnMoney(AccountEntry $toAccount,$value)
	{
		$accountService = new AccountEntryService();
		
		if(!is_numeric($value))
			throw new Exception("Invalid value to earn!");
			
		$transation = new Transaction();
		$transation->setFrom(null);
		$transation->setTo($toAccount);
		$transation->setValue($value);
		$transation->setCreatedBy(System::getUser());
		$transation->setUpdatedBy(System::getUser());
		$this->save($transation);
		
		$toAccount->setValue($toAccount->getValue()+ $value);
		$toAccount->setUpdatedBy(System::getUser());
		$accountService->save($toAccount);
	}
}
?>