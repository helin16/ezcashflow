<?php
class WapUserService
{
	public function login($vars)
	{
		$username=$vars["username"];
		$password=$vars["password"];
		$userAccountService = new UserAccountService();
		try
		{
			$useraccount=$userAccountService->getUserByUsernameAndPassword($username,$password,false);
			System::setUser($useraccount);
			header("Location: /");
		}
		catch(Exception $ex)
		{
			echo $ex->getMessage()."<br />";
			echo WapInterface::defaultPage();
		}
	}
	
	public function logout()
	{
		System::setUser(null);
		header("Location: /");
	}
	
	public function saveAccountEntry($vars)
	{
		$parentAccountId = $vars["accParentId"];
		$accountId = $vars["accId"];
		$accName = addslashes(trim($vars["accName"]));
		$accValue = str_replace(",","",$vars["accValue"]);
		$accComments= addslashes(trim($vars["accComments"]));
		
		if($accName=="")
			header("Location: /viewAccount/$accountId/Invalid_Account_Name!");
			
		//exsiting account
		if($parentAccountId=="")
		{
			$service = new AccountEntryService();
			$entry = $service->get($accountId);
			$entry->setName($accName);
			$entry->setValue($accValue);
			$entry->setComments($accComments);
			$entry->setUpdatedBy(System::getUser());
			$service->save($entry);
			
			header("Location: /viewAccount/$accountId/saved_successfully!");
		}
		else
		{
			if(!is_numeric($accValue))
				$accValue="0.0";
			$service = new AccountEntryService();
			$parentEntry = $service->get($parentAccountId);
			
			$accNo = $service->getNextAccountNo($parentEntry);
			
			
			//if this is the first child, then inherit all values from parent!
			$msg="";
			if(count($service->getChildrenAccounts($parentEntry))==0)
			{
				$accValue +=$parentEntry->getValue();
				$msg =str_replace(" ","_","As it is the first child, it inheritted '".$parentEntry->getValue()."' in its value!");
			}
			
			$entry = new AccountEntry();
			$entry->setName($accName);
			$entry->setValue($accValue);
			$entry->setAccountNumber($accNo);
			$entry->setComments($accComments);
			$entry->setRoot($parentEntry->getRoot());
			$entry->setParent($parentEntry);
			$entry->setCreatedBy(System::getUser());
			$entry->setUpdatedBy(System::getUser());
//			$userAccountId = System::getUser()->getId();
			$service->save($entry);
			
			$parentEntry->setValue("");
			$service->save($parentEntry);
			
			
//			$qry = "insert into accountentry(`name`,`value`,`accountNumber`,`comments`,`rootId`,`parentId`,`created`,`createdById`,`updated`,`updatedById`) 
//					values('$accName','$accValue','$accNo','$accComments','".$parentEntry->getRoot()->getId()."','".$parentEntry->getId()."',NOW(),'$userAccountId',NOW(),'$userAccountId')";
//			$sql = new SqlStatement();
//			$sql->setSQL($qry);
//			
//			$dao = new Dao();
//			$dao->execute($sql);
			
			header("Location: /viewAccount/".$entry->getId()."/added_successfully!$msg");
		}
	}
	
	public function deleteAccount($vars)
	{
		$accountId = $vars["accId"];
		
		$service = new AccountEntryService();
		$entry = $service->get($accountId);
		
		$entry->setActive(0);
		$service->save($entry);
		header("Location: /manageAccounts/".$entry->getRoot()->getId()."/deleted_successfully!");
	}
	
	public function spendMoney($vars)
	{
		$fromAccountId = $vars["fromAccountId"];
		$toAccountId = $vars["toAccountId"];
		$value = $vars["value"];
		$comments = $vars["comments"];
		
		$service = new AccountEntryService();
		$transactionService = new TransactionService();
		$transactionService->spendMoney($service->get($fromAccountId),$service->get($toAccountId),$value,$comments);
		
		header("Location: /loadDefaultPageWithMsg/Spend_Successfully!");
	}
	
	public function earnMoney($vars)
	{
		$toAccountId = $vars["toAccountId"];
		$value = $vars["value"];
		$comments = $vars["comments"];
		
		$service = new AccountEntryService();
		$transactionService = new TransactionService();
		$transactionService->earnMoney($service->get($toAccountId),$value,$comments);
		
		header("Location: /loadDefaultPageWithMsg/Earn_Successfully!");
	}
	
	public function reportTransaction($vars)
	{
		$fromDate = $vars["fromDate"];
		$toDate = $vars["toDate"];
		
		header("Location: /reports/range/0/$fromDate/$toDate");
	}
}
?>