<?php

class ProfitPanel extends TPanel  
{
	public function renderEndTag($writer)
	{
		$html = $this->loadAccounts();
		$writer->write($html);
		parent::renderEndTag($writer);
	}
	
	public function loadAccounts()
	{
		$transactionService = new TransactionService();
		
		$incomeAccountIds=array();
		$expenseAccountIds=array();
		
		$sql = "select id,rootId from accountentry where active = 1 and rootId in (3,4)";
		foreach(Dao::getResultsNative($sql,array(),PDO::FETCH_ASSOC) as $row)
		{
			if($row["rootId"]==3)
				$incomeAccountIds[] = $row["id"];
			else if($row["rootId"]==4)
				$expenseAccountIds[] = $row["id"];
		}
		
		// day
		$today = new HydraDate("now");
		$start = $today->getDateTime()->format('Y-m-d 00:00:00');
		$today->modify("+1 day");
		$end = $today->getDateTime()->format('Y-m-d 00:00:00');
		$day_start = $start;
		$day_end = $end;
		$day_income = $transactionService->getSumOfExpenseBetweenDates($start,$end,3);
		$day_expense = $transactionService->getSumOfExpenseBetweenDates($start,$end,4);
		$day_income = (trim($day_income)=="") ? 0 :$day_income;
		$day_expense = (trim($day_expense)=="") ? 0 :$day_expense;
		$day_diff=$day_income-$day_expense;
		
		// week
		$today = new HydraDate("now");
		$weekDay = $today->getDateTime()->format('N');
		if($weekDay>=4)
			$today->modify("-".($weekDay-4)." day");
		else
			$today->modify("-".(3+$weekDay)." day");
		$start = $today->getDateTime()->format("Y-m-d 00:00:00");
		$today->modify("+1 week");
		$end = $today->getDateTime()->format("Y-m-d 00:00:00");
		$week_start = $start;
		$week_end = $end;
		$week_income = $transactionService->getSumOfExpenseBetweenDates($start,$end,3);
		$week_expense = $transactionService->getSumOfExpenseBetweenDates($start,$end,4);
		$week_income = (trim($week_income)=="") ? 0 :$week_income;
		$week_expense = (trim($week_expense)=="") ? 0 :$week_expense;
		$week_diff=$week_income-$week_expense;
		
		// month
		$today = new HydraDate("now");
		$start = $today->getDateTime()->format("Y-m-01 00:00:00");
		$today->modify("+1 month");
		$end = $today->getDateTime()->format("Y-m-01 00:00:00");
		$month_start = $start;
		$month_end = $end;
		$month_income = $transactionService->getSumOfExpenseBetweenDates($start,$end,3);
		$month_expense = $transactionService->getSumOfExpenseBetweenDates($start,$end,4);
		$month_income = (trim($month_income)=="") ? 0 :$month_income;
		$month_expense = (trim($month_expense)=="") ? 0 :$month_expense;
		$month_diff=$month_income-$month_expense;
		
		// year
		$today = new HydraDate("now");
		$start = $today->getDateTime()->format("Y-01-01 00:00:00");
		$today->modify("+1 year");
		$end = $today->getDateTime()->format("Y-01-01 00:00:00");
		$year_start = $start;
		$year_end = $end;
		$year_income = $transactionService->getSumOfExpenseBetweenDates($start,$end,3);
		$year_expense = $transactionService->getSumOfExpenseBetweenDates($start,$end,4);
		$year_income = (trim($year_income)=="") ? 0 :$year_income;
		$year_expense = (trim($year_expense)=="") ? 0 :$year_expense;
		$year_diff=$year_income-$year_expense;
		
		// all
		$today = new HydraDate("now");
		$start = $today->getDateTime()->format("1791-01-01 00:00:00");
		$today->modify("+1 year");
		$end = $today->getDateTime()->format("9999-01-01 00:00:00");
		$all_start = $start;
		$all_end = $end;
		$all_income = $transactionService->getSumOfExpenseBetweenDates($start,$end,3);
		$all_expense = $transactionService->getSumOfExpenseBetweenDates($start,$end,4);
		$all_income = (trim($all_income)=="") ? 0 :$all_income;
		$all_expense = (trim($all_expense)=="") ? 0 :$all_expense;
		$all_diff=$all_income-$all_expense;
		
		$html="<table>";
			$html.="<tr style='background:#555555;color:#dddddd;'>";
				$html .="<td  width='60px'>&nbsp;</td>";
				$html .="<td width='100px'>
							Day<br />
							<i style='font-size:9px'>$day_start ~ <br />$day_end </i>
						</td>";
				$html .="<td width='100px'>
							Week<br />
							<i style='font-size:9px'>$week_start ~ <br />$week_end </i>
						</td>";
				$html .="<td width='100px'>
							Month<br />
							<i style='font-size:9px'>$month_start ~ <br />$month_end </i>
						</td>";
				$html .="<td width='100px'>
							Year<br />
							<i style='font-size:9px'>$year_start ~ <br />$year_end </i>
						</td>";
				$html .="<td width='100px'>
							All<br />
							<i style='font-size:9px'>$all_start ~ <br />$all_end </i>
						</td>";
			$html.="</tr>";
			$html.="<tr>";
				$html .="<td>Income</td>";
				$html .="<td>".$this->makeURLToReport("\$$day_income",array(),$incomeAccountIds,$day_start,$day_end)."</td>";
				$html .="<td>".$this->makeURLToReport("\$$week_income",array(),$incomeAccountIds,$week_start,$week_end)."</td>";
				$html .="<td>".$this->makeURLToReport("\$$month_income",array(),$incomeAccountIds,$month_start,$month_end)."</td>";
				$html .="<td>".$this->makeURLToReport("\$$year_income",array(),$incomeAccountIds,$year_start,$year_end)."</td>";
				$html .="<td>".$this->makeURLToReport("\$$all_income",array(),$incomeAccountIds,$all_start,$all_end)."</td>";
			$html.="</tr>";
			$html.="<tr style='background:#cccccc;'>";
				$html .="<td>Expense</td>";
				$html .="<td>".$this->makeURLToReport("\$$day_expense",array(),$expenseAccountIds,$day_start,$day_end)."</td>";
				$html .="<td>".$this->makeURLToReport("\$$week_expense",array(),$expenseAccountIds,$week_start,$week_end)."</td>";
				$html .="<td>".$this->makeURLToReport("\$$month_expense",array(),$expenseAccountIds,$month_start,$month_end)."</td>";
				$html .="<td>".$this->makeURLToReport("\$$year_expense",array(),$expenseAccountIds,$year_start,$year_end)."</td>";
				$html .="<td>".$this->makeURLToReport("\$$all_expense",array(),$expenseAccountIds,$all_start,$all_end)."</td>";
			$html.="</tr>";
			$html.="<tr style='font-weight:bold;'>";
				$html .="<td>Diff</td>";
				$html .="<td>$ $day_diff</td>";
				$html .="<td>$ $week_diff</td>";
				$html .="<td>$ $month_diff</td>";
				$html .="<td>$ $year_diff</td>";
				$html .="<td>$ $all_diff</td>";
			$html.="</tr>";
		$html.="</table>";
		return $html;
	}
	
	public function makeURLToReport($value,$fromAccountIds,$toAccountIds,$fromDate,$toDate)
	{
		$vars = array(
					"fromAccountIds"=>$fromAccountIds,
					"toAccountIds"=>$toAccountIds,
					"fromDate"=>$fromDate,
					"toDate"=>$toDate
				);
		$serial = serialize($vars);
		return "<a href='/reports/$serial'> $value</a>";
	}
}

?>