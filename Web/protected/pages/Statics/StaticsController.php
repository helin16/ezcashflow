<?php
class StaticsController extends EshopPage 
{
	public $onePercentageWidth=0.2;
	
	public function __construct()
	{
		parent::__construct();
		$this->menuItemName='statics';
	}
	
	public function onLoad($param)
	{
		if(!$this->IsPostBack)
		{
			$this->getBars($this->expense_bar,4);
			$this->getBars($this->income_bar,3);
		}
	}
	
	public function getBars(&$tabview,$rootId=4)
	{
		$sql ="select 
					(ceil((length(accountNumber)-1)/4)) len,
					acc.id,
					acc.name
				from accountentry acc
				where acc.active = 1
				and acc.accountNumber like '$rootId%'
				and acc.id!=$rootId
				order by trim(acc.accountNumber)";
		$result = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		
		$html ="<table width='100%'>";
			$rowNo=0;
			foreach($result as $row)
			{
				$value = $this->getValue($row["id"]);
				$html .="<tr ".($rowNo%2==0 ? "" : "style='background:#eeeeee;'").">";
					$html .="<td width='150px'  style='font-size:12px;'>";
						$html .=$row['len']==0 ? "" : str_repeat("&nbsp;&nbsp;",($row['len']));
						$html .=$row["name"];
					$html .="</td>";
					$html .="<td  style='font-size:10px;'>";
						$html .="<div style=\"float:left;position:relative;background:#00ff00; width:".round(((is_numeric($value) ? $value : 0)*$this->onePercentageWidth))."px; height:15px;\"></div>";
						$html .="&nbsp;$value";
					$html .="</td>";
				$html .="</tr>";
				$rowNo++;
			}
		$html .="</table>";
		$tabview->getControls()->add($html);
	}
	
	private function getValue($accountId)
	{
		$accountService = new AccountEntryService();
		$sql="select count(distinct acc.id) `count` from accountentry acc where acc.active = 1 and acc.parentId = $accountId";
		$result = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		$count = $result[0]["count"];
		$msg = $accountService->getChildrenValueSum($accountService->get($accountId));
		if($count>0)
			$msg = "<b>$msg</b>";
		return $msg;
	}
}
?>