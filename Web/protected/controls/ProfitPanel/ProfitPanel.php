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
		$html="<table width='100%'>";
			$html.="<tr style='background:black;color:white;'>";
				$html .="<td  width='60px'>&nbsp;</td>";
				$html .="<td>Day</td>";
				$html .="<td>Week</td>";
				$html .="<td>Month</td>";
				$html .="<td>Year</td>";
			$html.="</tr>";
		$transactionService = new TransactionService();
		
		// day
		$today = new HydraDate("now");
		$start = $today->getDateTime()->format('Y-m-d 00:00:00');
		$today->modify("+1 day");
		$end = $today->getDateTime()->format('Y-m-d 00:00:00');
		$day_income = $transactionService->getSumOfExpenseBetweenDates($start,$end,3);
		$day_expense = $transactionService->getSumOfExpenseBetweenDates($start,$end,4);
		$day_income = (trim($day_income)=="") ? 0 :$day_income;
		$day_expense = (trim($day_expense)=="") ? 0 :$day_expense;
		$day_diff=$day_income-$day_expense;
		
		// week
		$today = new HydraDate("now");
		$weekDay = $today->getDateTime()->format('W');
		$start = $today->getDateTime()->format("Y-m-01 00:00:00");
		$today->modify("+1 week");
		$end = $today->getDateTime()->format("Y-m-01 00:00:00");
		$week_income = $transactionService->getSumOfExpenseBetweenDates($start,$end,3);
		$week_expense = $transactionService->getSumOfExpenseBetweenDates($start,$end,4);
		$week_income = (trim($day_income)=="") ? 0 :$day_income;
		$week_expense = (trim($day_expense)=="") ? 0 :$day_expense;
		$week_diff=$week_income-$week_expense;
		
		// month
		$today = new HydraDate("now");
		$start = $today->getDateTime()->format("Y-m-01 00:00:00");
		$today->modify("+1 month");
		$end = $today->getDateTime()->format("Y-m-01 00:00:00");
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
		$year_income = $transactionService->getSumOfExpenseBetweenDates($start,$end,3);
		$year_expense = $transactionService->getSumOfExpenseBetweenDates($start,$end,4);
		$year_income = (trim($year_income)=="") ? 0 :$year_income;
		$year_expense = (trim($year_expense)=="") ? 0 :$year_expense;
		$year_diff=$year_income-$year_expense;
		
			$html.="<tr>";
				$html .="<td>Income</td>";
				$html .="<td>$ $day_income</td>";
				$html .="<td>$ $week_income</td>";
				$html .="<td>$ $month_income</td>";
				$html .="<td>$ $year_income</td>";
			$html.="</tr>";
			$html.="<tr style='background:#cccccc;'>";
				$html .="<td>Expense</td>";
				$html .="<td>$ $day_expense</td>";
				$html .="<td>$ $week_expense</td>";
				$html .="<td>$ $month_expense</td>";
				$html .="<td>$ $year_expense</td>";
			$html.="</tr>";
			$html.="<tr style='font-weight:bold;'>";
				$html .="<td>Diff</td>";
				$html .="<td>$ $day_diff</td>";
				$html .="<td>$ $week_diff</td>";
				$html .="<td>$ $month_diff</td>";
				$html .="<td>$ $year_diff</td>";
			$html.="</tr>";
		$html.="</table>";
		return $html;
	}
}

?>