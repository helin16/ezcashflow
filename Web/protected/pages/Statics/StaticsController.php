<?php
class StaticsController extends EshopPage 
{
	public $onePercentageWidth=0.1;
	
	public function __construct()
	{
		parent::__construct();
		$this->menuItemName='statics';
	}
	
	public function onLoad($param)
	{
		if(!$this->IsPostBack)
		{
			$this->getBars($this->expense_bar,4,"40001");
			$this->getBars($this->investment_bar,4,"40002");
			$this->getBars($this->income_bar,3);
		}
	}
	
	public function getBars($tabview=null,$rootId=4,$likeAccountNo="")
	{
		$sql ="select 
					(ceil((length(accountNumber)-1)/4)) len,
					acc.id,
					acc.name,
					acc.budget
				from accountentry acc
				where acc.active = 1
				".($likeAccountNo!="" ? "and acc.accountNumber like '$likeAccountNo%'" : "and acc.accountNumber like '$rootId%'")."
				order by trim(acc.accountNumber)";
		$result = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		
		$html ="<table width='100%'>";
			$rowNo=0;
			foreach($result as $row)
			{
				$value = $this->getValue($row["id"]);
				$budget = $row["budget"];
				$color = (intval($budget)!=0 && $budget < $value) ? "#ff0000" : "#00ff00";
				
				$html .="<tr ".($rowNo%2==0 ? "" : "style='background:#eeeeee;'").">";
					$html .="<td width='150px'  style='font-size:12px;'>";
						$html .=$row['len']==0 ? "" : str_repeat("&nbsp;&nbsp;",($row['len']));
						$html .=$row["name"];
					$html .="</td>";
					$html .="<td  style='font-size:10px;'>";
						$html .="<div style=\"float:left;position:relative;background:$color; width:".round(((is_numeric($value) ? $value : 0)*$this->onePercentageWidth))."px; height:15px;\"></div>";
						$html .="&nbsp;$ $value ";
						if($budget!=0 && $budget<$value)
							$html .= "($ $budget)";
					$html .="</td>";
				$html .="</tr>";
				$rowNo++;
			}
		$html .="</table>";
		$tabview->getControls()->add($html);
	}
	
	
	public function toExcel($sender,$param)
	{
		$rootId=trim($param->CommandParameter);
		$sql ="select 
					distinct acc.id,
					acc.name
				from accountentry acc
				left join accountentry acc_children on (acc_children.parentId = acc.id and acc_children.active=1)
				where acc.active = 1
				and acc.accountNumber like '$rootId%'
				and acc_children.id is null
				order by trim(acc.accountNumber)
				";
		$result = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		
		$html ="<table border='1'>";
	    	$html .="<tr style='background:black;color:white;'>";
		    	$html .="<td>AccountName</td>";
		    	$html .="<td>Value</td>";
	    	$html .="</tr>";
	    	foreach($result as $row)
	    	{
	    		$value = $this->getValue($row["id"]);
	    		$name=$this->getAccountBreadcrumb($row["id"]);
	    		$html .="<tr >";
			    	$html .="<td>$name</td>";
			    	$html .="<td>$value</td>";
	    		$html .="</tr>";
	    	}
    	$html .="</table>";
		
		$export_file = "accounts.xls";
	    ob_end_clean();
	    ini_set('zlib.output_compression','Off');
	   
	    header('Pragma: public');
	    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");                  // Date in the past   
	    header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
	    header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1
	    header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
	    header ("Pragma: no-cache");
	    header("Expires: 0");
	    header('Content-Transfer-Encoding: none');
	    header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera
	    header("Content-type: application/x-msexcel");                    // This should work for the rest
	    header('Content-Disposition: attachment; filename="'.basename($export_file).'"'); 
	    echo $html;
	    die;
	}
	
	private function getValue($accountId)
	{
		$accountService = new AccountEntryService();
		$account = $accountService->get($accountId);
		if(!$account instanceof AccountEntry )
			return 0;
		$now = new UDate();
		$sql="select Unix_timestamp(created) `created` from transaction order by created asc limit 1";
		$result = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		$diff_secs = $now->getUnixTimeStamp() - $result[0]['created'];
		
		$sql = "select if(sum(tt.value) is null,0,sum(tt.value)) `sum` 
				from transaction tt 
				where tt.active = 1 and tt.toId in 
				(
					select acc.id from accountentry acc where acc.active = 1 and acc.accountNumber like '{$account->getAccountNumber()}%'
				)";
		$result = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		$msg = $result[0]["sum"];
		
		
		$msg = round(($msg/$diff_secs)*3600*24*365 /12,2);
		return $msg;
	}
	
	private function getAccountBreadcrumb($accountId)
	{
		$name = "";
		$sql="select acc.id,acc.name,acc.rootId,acc.parentId from accountentry acc where acc.id=$accountId";
		$result = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		if(count($result)==0)
			return "";
		
		$row = $result[0];
		if($row["id"]==$row["rootId"])
			return $row["name"];
			
		$name = $this->getAccountBreadcrumb($row["parentId"])." - ".$row["name"];
		return $name;
	}
}
?>