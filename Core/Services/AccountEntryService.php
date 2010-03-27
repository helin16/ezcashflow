<?php
class AccountEntryService extends BaseService 
{
	public function __construct()
	{
		parent::__construct("AccountEntry");
	}
	
	public function getNextAccountNo(AccountEntry $parent)
	{
		$sql = new SqlStatement();
		$sql->setDoResults(true);
		$sql->setSQL("select max(accountNumber) as max from accountentry where parentId = ".$parent->getId());
		
		$dao = new Dao();
		$dao->execute($sql);
		$results = $sql->getResultSet();
		if(count($results)==0)
			return $parent->getAccountNumber()."0001";
		else if($results[0]["max"]=="")
			return $parent->getAccountNumber()."0001";
		
		return str_pad($results[0]["max"]+1,4,"0",STR_PAD_LEFT);
	}
	
	public function getChildrenAccounts(AccountEntry $parent,$includeSelf=false,$includeAll=false)
	{
		if($includeAll)
			return $this->findByCriteria(($includeSelf==false ? "id!=".$parent->getId()." AND " : "")."accountNumber like '".$parent->getAccountNumber()."%'");
		else
			return $this->findByCriteria("parentId=".$parent->getId());
	}
	
	public function getChildrenValueSum(AccountEntry $parent,$includeAll=false)
	{
		$sql ="select sum(value) as sum from accountentry where active = 1";
		if($includeAll)
			$sql.=" and accountNumber like '".$parent->getAccountNumber()."%'";
		else
			$sql.=" and parentId=".$parent->getId();
			
		$sql = new SqlStatement();
		$sql->setDoResults(true);
		$sql->setSQL("select max(accountNumber) as max from accountentry where parentId = ".$parent->getId());
		
		$dao = new Dao();
		$dao->execute($sql);
		$results = $sql->getResultSet();
		return $results[0]["sum"];
	}
	
	public function getAllAccountInOrder($rootId=1)
	{
		$sql = new SqlStatement();
		$sql->setDoResults(true);
		$sql->setSQL("select ac.id,ac.name,ac.accountNumber,ac.value,ac.parentId,
					FLOOR(CHAR_LENGTH(ac.accountNumber)/4) as noOfSpaces,
					(select count(acc.id) from accountentry acc where acc.parentId = ac.id and acc.active = 1) as countChildren 
					from accountentry ac where ac.rootId = $rootId
					order by ac.rootId asc, LCASE(ac.accountNumber) asc");
		
		$dao = new Dao();
		$dao->execute($sql);
		$results = $sql->getResultSet();
		return $results;
	}
	
	public function getAllLeavesForType($rootId=1)
	{
		$sql = new SqlStatement();
		$sql->setDoResults(true);
		$sql->setSQL("select ac.id,ac.name,ac.value
					from accountentry ac 
					where ac.rootId = $rootId
					and ((select if(count(acc.id)=0,1,0) from accountentry acc where acc.parentId = ac.id and acc.active = 1))=1
					order by ac.name asc");
		
		$dao = new Dao();
		$dao->execute($sql);
		$results = $sql->getResultSet();
		return $results;
	}
}
?>