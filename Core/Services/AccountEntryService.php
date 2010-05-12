<?php
class accountentryService extends BaseService 
{
	public function __construct()
	{
		parent::__construct("AccountEntry");
	}
	
	public function getNextAccountNo(accountentry $parent)
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
	
	public function getChildrenAccounts(accountentry $parent,$includeSelf=false,$includeAll=false)
	{
		if($includeAll)
			return $this->findByCriteria(($includeSelf==false ? "id!=".$parent->getId()." AND " : "")."accountNumber like '".$parent->getAccountNumber()."%'");
		else
			return $this->findByCriteria("parentId=".$parent->getId());
	}
	
	public function getChildrenValueSum(accountentry $parent,$includeAll=false)
	{
		$sql ="select sum(value) as sum from accountentry where active = 1";
		if($includeAll)
			$sql.=" and accountNumber like '".$parent->getAccountNumber()."%'";
		else
			$sql.=" and parentId=".$parent->getId();
			
		$results = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		return $results[0]["sum"];
	}
}
