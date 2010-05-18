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
	
	public function getBars($tabview=null,$rootId=4)
	{
		$sql ="select 
					(ceil((length(accountNumber)-1)/4)) len,
					acc.id,
					acc.name
				from accountentry acc
				where acc.active = 1
				and acc.accountNumber like '$rootId%'
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
		$sql="select acc.accountNumber,
				(select count(distinct acc_c.id) from accountentry acc_c where acc_c.active = 1 and acc_c.parentId = acc.id) `count`
				from accountentry acc 
				where acc.active = 1 and acc.id = $accountId";
		$result = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		$count = $result[0]["count"];
		$accounNumber = $result[0]["accountNumber"];
		
		$sql = "select if(sum(tt.value) is null,0,sum(tt.value)) `sum` 
				from transaction tt 
				inner join accountentry acc on (acc.active = 1 and acc.accountNumber like '$accounNumber%' and tt.toId=acc.id)
				where tt.active = 1 ";
		$result = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		$msg = $result[0]["sum"];
		
		$sql = "select 	TIMESTAMPDIFF(second,min(t.created),max(t.created)) `diff_day` from transaction t where t.active=1";
		$result = Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC);
		$diff_days = $result[0]["diff_day"];
		
		$msg = round(($msg/$diff_days)*3600*24*365 /12,2);
		if($count>0)
			$msg = "<b>$msg</b>";
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