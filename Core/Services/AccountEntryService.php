<?php
class AccountEntryService extends BaseService 
{
	public function __construct()
	{
		parent::__construct("AccountEntry");
	}
	
	public function getNextAccountNo(accountentry $parent)
	{
		$sql ="select max(accountNumber) as max from accountentry where parentId = ".$parent->getId();
		$results = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
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
	
	public function getChildrenValueSum(accountentry $parent,$includeSelf=true)
	{
		$sql = "
				select 
					round(sum(
							if(acc.value='',0,acc.value)
							+
							(select if(sum(tt.value) is null,0,sum(tt.value)) from transaction tt where tt.active = 1 and tt.toId = acc.id)
							-
							(select if(sum(tf.value) is null,0,sum(tf.value)) from transaction tf where tf.active = 1 and tf.fromId = acc.id)
						),2) as `sum`
				from accountentry acc
				where acc.active = 1 
				and ".($includeSelf==false ? "acc.id!=".$parent->getId()." AND " : "")." acc.accountNumber like '".$parent->getAccountNumber()."%'";
//		return $parent->getId()."  ".$sql;
		$results = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		return $results[0]["sum"];
	}
}