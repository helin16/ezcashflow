<?php
class WapInterface
{
	public static function defaultPage($beforeLogin=true,$info="")
	{
		if($beforeLogin)
		{
			return "<form action='/post/WapUserService/login' method='POST'>
					<table style='border:5px #cccccc solid;'>
						<tr>
							<td colspan='2'>
								<!-- Welcome to EZ Cash Flow! --> 
							</td>
						</tr>
						<tr>
							<td width='100px'>
								Username:
							</td>
							<td>
								<input type='text' name='username' />
							</td>
						</tr>
						<tr>
							<td>
								Password:
							</td>
							<td>
								<input type='password' name='password' />
							</td>
						</tr>
						<tr>
							<td>
								<input type='submit' value='Login' />
							</td>
						</tr>
					</table>
				</form>";
		}
		else
		{
			$transactionService = new TransactionService();
			$today =$temp= new DateTime();
			$startOfToday = $today->format('Y-m-d');
			$temp->modify("+1 day");
			$endOfToday = $temp->format('Y-m-d');

			$thisWeek = new DateTime(self::week_start_date($today->format('W'),$today->format('Y')));
			$startOfWeek = $thisWeek->format("Y-m-d");
			$thisWeek->modify("+1 week");
			$endOfWeek = $thisWeek->format("Y-m-d");
			
			$thisMonth = new DateTime($today->format('Y-m')."-01");
			$startOfMonth = $thisMonth->format("Y-m-d");
			$thisMonth->modify("+1 month");
			$endOfMonth = $thisMonth->format("Y-m-d");
			
			$thisYear = new DateTime($today->format('Y')."-01-01");
			$startOfYear = $thisYear->format("Y-m-d");
			$thisYear->modify("+1 year");
			$endOfYear = $thisYear->format("Y-m-d");
			
			$income_day = $transactionService->getSumOfExpenseBetweenDates($startOfToday,$endOfToday,3);
			$income_week = $transactionService->getSumOfExpenseBetweenDates($startOfWeek,$endOfWeek,3);
			$income_month = $transactionService->getSumOfExpenseBetweenDates($startOfMonth,$endOfMonth,3);
			$income_year = $transactionService->getSumOfExpenseBetweenDates($startOfYear,$endOfYear,3);
			
			$expense_day = $transactionService->getSumOfExpenseBetweenDates($startOfToday,$endOfToday,4);
			$expense_week = $transactionService->getSumOfExpenseBetweenDates($startOfWeek,$endOfWeek,4);
			$expense_month = $transactionService->getSumOfExpenseBetweenDates($startOfMonth,$endOfMonth,4);
			$expense_year = $transactionService->getSumOfExpenseBetweenDates($startOfYear,$endOfYear,4);
			
			$diff_day=$income_day-$expense_day;
			$diff_week=$income_week-$expense_week;
			$diff_month=$income_month-$expense_month;
			$diff_year=$income_year-$expense_year;
			
			return "
					<table width=\"100%\">
							<tr>
								<td>
									".self::getMenu()."
								</td>
							</tr>
							<tr>
								<td>
									<b style='color:green'>$info</b><br />
								</td>
							</tr>
							<tr>
								<td>
									<div style='padding:15px;'>
										<fieldset>
											<legend>Summary Of Expense/Income</legend>
												<table width=\"100%\">
													<tr style='background:#000000;color:#ffffff;height:34px;'>
														<td>
															&nbsp;
														</td>
														<td>
															Day
														</td>
														<td>
															Week
														</td>
														<td>
															Month
														</td>
														<td>
															Year
														</td>
													</tr>
													<tr>
														<td>Income</td>
														<td>
															<a href='/reports/day/3'>$".self::getCurrency($income_day)."</a>
														</td>
														<td>
															<a href='/reports/week/3'>$".self::getCurrency($income_week)."</a>
														</td>
														<td>
															<a href='/reports/month/3'>$".self::getCurrency($income_month)."</a>
														</td>
														<td>
															<a href='/reports/year/3'>$".self::getCurrency($income_year)."</a>
														</td>
													</tr>
													<tr style='background:#cccccc;'>
														<td>Expense</td>
														<td>
															<a href='/reports/day/4'>$".self::getCurrency($expense_day)."</a>
														</td>
														<td>
															<a href='/reports/week/4'>$".self::getCurrency($expense_week)."</a>
														</td>
														<td>
															<a href='/reports/month/4'>$".self::getCurrency($expense_month)."</a>
														</td>
														<td>
															<a href='/reports/year/4'>$".self::getCurrency($expense_year)."</a>
														</td>
													</tr>
													<tr style='font-weight:bold;'>
														<td>Diff</td>
														<td>$".self::getCurrency($diff_day)."</td>
														<td>$".self::getCurrency($diff_week)."</td>
														<td>$".self::getCurrency($diff_month)."</td>
														<td>$".self::getCurrency($diff_year)."</td>
													</tr>
												</table>
										</fieldset>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div style='padding:15px;'>
										<fieldset>
											<legend>Expense</legend>
											<form action='/post/WapUserService/spendMoney' method='POST'>
												<table width=\"100%\" style='background:#cccccc;'>
													<tr>
														<td width='100px'>
															From:
														</td>
														<td>
															".self::getDropDownListForAccounts("fromAccountId",array(1,2))."
														</td>
													</tr>
													<tr>
														<td width='100px'>
															To:
														</td>
														<td>
															".self::getDropDownListForAccounts("toAccountId",array(4))."
														</td>
													</tr>
													<tr>
														<td width='100px'>
															Value:
														</td>
														<td>
															<input type='text' style='width:100%' name='value'/>
														</td>
													</tr>
													<tr>
														<td>
															Description:
														</td>
														<td>
															<input type='text' style='width:100%' name='comments'/>
														</td>
													</tr>
													<tr>
														<td>
															&nbsp;
														</td>
														<td>
															<input type='submit' value='save'/>
														</td>
													</tr>
												</table>
											</form>
										</fieldset>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div style='padding:15px;'>
										<fieldset>
											<legend>Income</legend>
											<form action='/post/WapUserService/earnMoney' method='POST'>
												<table width=\"100%\" style='background:#cccccc;'>
													<tr>
														<td width='100px'>
															Into:
														</td>
														<td>
															".self::getDropDownListForAccounts("fromAccountId",array(3))."
														</td>
													</tr>
													<tr>
														<td>
															Bank:
														</td>
														<td>
															".self::getDropDownListForAccounts("toAccountId",array(1))."
														</td>
													</tr>
													<tr>
														<td width='100px'>
															Value:
														</td>
														<td>
															<input type='text' style='width:100%' name='value'/>
														</td>
													</tr>
													<tr>
														<td>
															Description:
														</td>
														<td>
															<input type='text' style='width:100%' name='comments'/>
														</td>
													</tr>
													<tr>
														<td>
															&nbsp;
														</td>
														<td>
															<input type='submit' value='save'/>
														</td>
													</tr>
												</table>
											</form>
										</fieldset>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div style='padding:15px;'>
										<fieldset>
											<legend>Transfer</legend>
											<form action='/post/WapUserService/spendMoney' method='POST'>
												<table width=\"100%\" style='background:#cccccc;'>
													<tr>
														<td width='100px'>
															From:
														</td>
														<td>
															".self::getDropDownListForAccounts("fromAccountId",array(1,2))."
														</td>
													</tr>
													<tr>
														<td width='100px'>
															To:
														</td>
														<td>
															".self::getDropDownListForAccounts("toAccountId",array(2,1))."
														</td>
													</tr>
													<tr>
														<td width='100px'>
															Value:
														</td>
														<td>
															<input type='text' style='width:100%' name='value'/>
														</td>
													</tr>
													<tr>
														<td>
															Description:
														</td>
														<td>
															<input type='text' style='width:100%' name='comments'/>
														</td>
													</tr>
													<tr>
														<td>
															&nbsp;
														</td>
														<td>
															<input type='submit' value='save'/>
														</td>
													</tr>
												</table>
											</form>
										</fieldset>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									".self::getMenu(0,false)."
								</td>
							</tr>
						</table>";
		}
	}
	
	public static function loadDefaultPageWithMsg($vars)
	{
		$info = html_entity_decode(isset($vars[2]) && $vars[2]!="" ? $vars[2] : "");
		return self::defaultPage(!System::getUser() instanceof UserAccount,$info);
	}
	
	public static function manageAccounts($vars)
	{
		$typeId = (isset($vars[2]) && is_numeric($vars[2])) ? $vars[2] : 1;
		$info = html_entity_decode(isset($vars[3]) && $vars[3]!="" ? $vars[3] : "");
		
		return "<table width=\"100%\">
					<tr>
						<td>
							".self::getMenu(1)."
						</td>
					</tr>
					<tr>
						<td>
							<b style='color:green'>$info</b><br />
							".self::getAccountTable($typeId)."
						</td>
					</tr>
					<tr>
						<td>
							".self::getMenu(1,false)."
						</td>
					</tr>
				</table>";
	}
	
	public static function viewAccount($vars)
	{
		$accountId = $vars[2];
		$service = new AccountEntryService();
		$entry = $service->get($accountId);
		if(!$entry instanceof AccountEntry)
			return "Invalid account!";
			
		$info = html_entity_decode(isset($vars[3]) && $vars[3]!="" ? $vars[3] : "");
		return "
				<table width=\"100%\">
					<tr>
						<td>
							".self::getMenu(1)."
						</td>
					</tr>
					<tr>
						<td>
							<b style='color:green'>$info</b><br />
							<a href='/manageAccounts/'>Back</a> 
							&nbsp;&nbsp; | &nbsp;&nbsp; <a href='/addAccount/".$entry->getId()."'>Create New Account Under '".$entry->getName()."'</a>
							".self::getViewAccountTable($accountId,$entry->getName(),$entry->getAccountNumber(),$entry->getValue(),$entry->getComments())."
							<a href='/manageAccounts/'>Back</a>
							&nbsp;&nbsp; | &nbsp;&nbsp; <a href='/addAccount/".$entry->getId()."'>Create New Account Under '".$entry->getName()."'</a>
						</td>
					</tr>
					<tr>
						<td>
							".self::getMenu(1,false)."
						</td>
					</tr>
				</table>";
	}
	
	public static function addAccount($vars)
	{
		$parentAccountId = $vars[2];
		$service = new AccountEntryService();
		$entry = $service->get($parentAccountId);
		if(!$entry instanceof AccountEntry)
			return "Invalid parent account!";
			
		$info = html_entity_decode(isset($vars[3]) && $vars[3]!="" ? $vars[3] : "");
		return "
				<table width=\"100%\">
					<tr>
						<td>
							".self::getMenu(1)."
						</td>
					</tr>
					<tr>
						<td>
							<b style='color:green'>$info</b><br />
							<a href='/manageAccounts/'>Back</a>
							".self::getViewAccountTable("","","","","",$parentAccountId)."
							<a href='/manageAccounts/'>Back</a>
						</td>
					</tr>
				</table>";
	}
	
	public static function deleteAccount($vars)
	{
		$accountId = $vars[2];
		$service = new AccountEntryService();
		$entry = $service->get($accountId);
		if(!$entry instanceof AccountEntry)
			return "Invalid account!";
			
		return "
				<form action='/post/WapUserService/deleteAccount/' Method='POST'>
					<table width=\"100%\">
						<tr>
							<td>
								<input type='hidden' name='accId' value='$accountId' />
								Are you sure you want to delete account:<br /><br />
								Name: ".$entry->getName()."<br />
								Account No: ".$entry->getAccountNumber()."<br />
								Value: ".$entry->getValue()."<br />
								Comments: ".$entry->getComments()."<br />
							</td>
						</tr>
						<tr>
							<td>
								<input type='submit' value='YES' />
								<a href='/viewAccount/$accountId'>NO</a>
							</td>
						</tr>
					</table>
				</form>";
	}
	
	public static function reports($vars)
	{
		$searchType=(isset($vars[2]) ? $vars[2] : "");
		
		return "<table width=\"100%\">
					<tr>
						<td>
							".self::getMenu(2)."
						</td>
					</tr>
					".($searchType!="" ? "":  "
					<tr>
						<td>
							
							<form action='/post/WapUserService/reportTransaction' method='POST'>
								<table width=\"100%\" style=\"background:#cccccc;\">
									<tr>
										<td colspan='2'>
											<b>Reporting Transactions:</b>
										</td>
									</tr>
									<tr>
										<td width='100px'>
											From Date:
										</td>
										<td>
											<input type='text' name='fromDate' />(YYYY-MM-DD)
										</td>
									</tr>
									<tr>
										<td>
											To Date:
										</td>
										<td>
											<input type='text' name='toDate' />(YYYY-MM-DD)
										</td>
									</tr>
									<tr>
										<td>
											&nbsp;
										</td>
										<td>
											<input type='submit' value='search' />
										</td>
									</tr>
								</table>
							</form>
						</td>
					</tr> ")."
					<tr>
						<td style='border:1px #cccccc solid;padding:15px;'>
							".self::showReports($vars)."
						</td>
					</tr>
					<tr>
						<td>
							".self::getMenu(2,false)."
						</td>
					</tr>
				</table>";
	}
	
	public static function getMenu($heightlightIndex=0,$showWelcome=true)
	{
		$i=0;
		return "
				<style>
					ul#menu
					{
						list-style:none;
						margin:0px;
						padding:0px;
						width:100%;
					}
					ul#menu li
					{
						float:left;
						padding: 0 15px; 0 0;
					}
					ul#menu li a
					{
					}
				</style>
				".($showWelcome ? "Welcome, ".System::getUser()."<br />" : "")."
				<ul id='menu'>
					<li><a href='/' ".self::showHightlight($i++,$heightlightIndex).">Home</a></li>
					<li><a href='/manageAccounts/' ".self::showHightlight($i++,$heightlightIndex).">Accounts</a></li>
					<li><a href='/reports/' ".self::showHightlight($i++,$heightlightIndex).">Reports</a></li>
					<li><a href='/post/WapUserService/logout' ".self::showHightlight($i++,$heightlightIndex).">Logout</a></li>
				</ul>
				";
	}
	
	public static function showHightlight($index,$wanttedIndex)
	{
		if($index==$wanttedIndex)
			return "style='text-decoration:underline;'";
		else
			return "style='text-decoration:none;'";
	}
	
	public static function getAccountTable($typeId=1,$page=null,$pagesize=30)
	{
		$service = new AccountEntryService();
		$rootEntry = $service->get($typeId);
		if(!$rootEntry instanceof AccountEntry)
			return "Invalid Type!";
			
		$entries = $service->getAllAccountInOrder($typeId);
		
		if(count($entries)==0)
			return "Nothing found!";
			
		$table = "
				<br />
				<a href='/manageAccounts/'>Assets</a>&nbsp;&nbsp;| &nbsp;&nbsp;
				<a href='/manageAccounts/2'>Liability</a>&nbsp;&nbsp;| &nbsp;&nbsp;
				<a href='/manageAccounts/3'>Income</a>&nbsp;&nbsp;| &nbsp;&nbsp;
				<a href='/manageAccounts/4'>Expense</a>
				<br />
				<b>All accounts under '".$rootEntry->getName()."':</b><br />
				<table width=\"100%\">
					<tr style='background:#000000;color:#ffffff;height:34px;'>
						<td >
							Account Name
						</td>
						<td width='150px'>
							Account No
						</td>
						<td width='100px'>
							Value
						</td>
						<td width='5%'>
							&nbsp;
						</td>
					</tr>";
		$rowNo=0;
		foreach($entries as $row)
		{
			$table .="<tr ".($rowNo %2 ==0 ? "": " style='background:#cccccc;'" ).">
						<td>
							".str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$row["noOfSpaces"]).($row["noOfSpaces"]==0? "" : " - ")."<a href='/viewAccount/".$row["id"]."'>".$row["name"]."</a>
						</td>
						<td>
							".$row["accountNumber"]."
						</td>
						<td>
							".(trim($row["value"])==""? "" : "$ ".self::getCurrency($row["value"]))."
						</td>
						<td width='8%'>
							".(trim($row["value"])==""? "" : "<a href='/reports/account/{$row["id"]}'>Trans.</a>&nbsp;")."
							".(
									(
										$row['countChildren']==0 
										&& $row['parentId']!= NULL
										&& $row['rootId']!= $row['id']
									) ? "<a href='/deleteAccount/".$row["id"]."'> Del</a>" : ""
								)."
						</td>
					</tr>";
			$rowNo++;
		}
		
		$table .="</table>";
		return $table;
	}
	
	public static function getViewAccountTable($accountId,$accName,$accNo,$accValue,$accComments,$accParentId="")
	{
		$accountService = new AccountEntryService();
		$value = self::getCurrency($accValue);
		$table = "
				<form action='/post/WapUserService/saveAccountEntry' method='POST'/>
					<table width=\"100%\" style='background:#aaaaaa;'>
						<tr>
							<td width='110px'>
								Account Name:
							</td>
							<td>
								<input type='hidden' name='accParentId' value='$accParentId'/>
								<input type='hidden' name='accId' value='$accountId'/>
								<input type='text' name='accName' value='$accName' style='width:100%;'/>
							</td>
						</tr>	
						<tr>
							<td>
								Account No.:
							</td>
							<td>
								<input type='hidden' name='accNo' value='$accNo'/>
								$accNo
							</td>
						</tr>	
						<tr>
							<td>
								Value:
							</td>
							<td>
								".($value=="" ? 
									"<input type='hidden' name='accValue' value='$value' />
									" 
								: "$ <input type='text' name='accValue' value='$value' style='width:90%;' />")."
							</td>
						</tr>
						<tr>
							<td>
								Comments:
							</td>
							<td>
								<input type='text' name='accComments' value='$accComments' style='width:100%;' />
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;
							</td>
							<td>
								<input type='submit' value='Save'/> &nbsp; &nbsp;
								".(
									(
										$accountId!="" 
										&& count($accountService->getChildrenAccounts($accountService->get($accountId)))==0 
										&& $accountService->get($accountId)->getParent() instanceof  AccountEntry 
									) ? "<a href='/deleteAccount/$accountId'> Delete </a>" : ""
								)."
							</td>
						</tr>
					</table>
				</form>";
		return $table;
	}
	
	public static function getCurrency($value)
	{
		if(trim($value)=="")
			return "";
		$sign="";
		if(strstr($value,"-"))
		{
			$sign="-";
			$value = substr($value,1);
		}
		
		
		list($wholeNo,$decimal) = explode(".",$value);
		if($decimal=="")
			$decimal="00";
		$wholeNo = strrev($wholeNo);
		$newWholeNos = array();
		$temp ="";
		for($i=0;$i<strlen($wholeNo);$i++)
		{
			if($i%3==0)
			{
				$newWholeNos[] = $temp;
				$temp ="";
			}
			$temp .=$wholeNo[$i];
		}
		$newWholeNos[] = $temp;
		unset($newWholeNos[0]);
		return $sign.strrev(implode(",",$newWholeNos)).".".$decimal;
	}
	
	public static function getDropDownListForAccounts($htmlName,$typeIds=array())
	{
		$service = new AccountEntryService();
		$list = "<select name='$htmlName'>";
		foreach($typeIds as $typeId)
		{
			$results = $service->getAllLeavesForType($typeId);
			foreach($results as $row)
			{
				$list .= "<option value='{$row["id"]}'>{$row['name']} - \${$row['value']}</option>";
			}
		}
		$list .= "</select>";
		return $list;
	}
	
	public static function showReports($vars)
	{
		$searchType=(isset($vars[2]) ? $vars[2] : "");
		$accountTypeId=(isset($vars[3]) && trim($vars[3])!=0 ? $vars[3] : "");
		if($searchType=="")
			return "Invalid Search Type!";
			
		$accountId = "";
			
		$fromDate = new DateTime();
		$toDate = new DateTime();
		switch(strtolower($searchType))
		{
			case 'year':
				{
					$fromDate->setDate($toDate->format('Y'),$toDate->format('m'),1);
					$toDate->modify('+1 year');
					$year = $toDate->format('Y');
					$month = $toDate->format('m');
					$toDate->setDate($year,$month,1);
					break;
				}
			case 'month':
				{
					$fromDate->setDate($toDate->format('Y'),$toDate->format('m'),1);
					$toDate->modify('+1 month');
					$year = $toDate->format('Y');
					$month = $toDate->format('m');
					$toDate->setDate($year,$month,1);
					break;
				}
			case 'week':
				{
					$year = $fromDate->format('Y');
					$week = $fromDate->format('W');
					
					$fromDate = new DateTime(self::week_start_date($week,$year));
					$toDate = new DateTime(self::week_start_date($week,$year));
					$toDate->modify("+1 week");
					break;
				}
			case 'day':
				{
					$toDate->modify('+1 day');
					break;
				}
			case 'range':
				{
					$fromDate = new DateTime(trim($vars[4]));
					$toDate = new DateTime(trim($vars[5]));
					break;
				}
			case 'account':
				{
					$fromDate = new DateTime("1990-01-01");
					$toDate = new DateTime("3999-01-01");
					$accountId=$accountTypeId;
					$accountTypeId="";
					break;
				}
		}
		$transactionService = new TransactionService();
		$accountService = new AccountEntryService();
		
		$where ="created >='".$fromDate->format("Y-m-d")."' and created < '".$toDate->format("Y-m-d")."'";
		$title ="Transactions created between '".$fromDate->format("Y-m-d")."' and '".$toDate->format("Y-m-d")."'";
		if($accountTypeId!="")
		{
			$where .=" AND toId in(select distinct id from AccountEntry where active=1 and rootId = $accountTypeId)";
			$title .=" and Transaction for '".$accountService->get($accountTypeId)->getName()."'";
		}
		
		if($accountId!="")
		{
			$where .=" AND (toId=$accountId or fromId=$accountId)";
			$title .=" and Transaction for '".$accountService->get($accountId)->getName()."'";
		}
		
		$result = $transactionService->findByCriteria($where,true,null,30,array("names"=>"id","direction"=>"desc"));
		$table ="<b>$title</b><br />
				<table width=\"100%\">";
					$table .="<tr style='background:#000000;color:#ffffff;height:34px;'>";
						$table .="<td width=\"10%\">Date</td>";
						$table .="<td width=\"15%\">From Acc.</td>";
						$table .="<td width=\"15%\">To Acc.</td>";
						$table .="<td width=\"10%\">Value</td>";
						$table .="<td>Description</td>";
					$table .="</tr>";
					$rowNo=0;
					$total =0;
					foreach($result as $transaction)
					{
						$table .="<tr ".($rowNo %2 ==0 ? "": " style='background:#cccccc;'" ).">";
							$table .="<td>".$transaction->getCreated()."</td>";
							$fromAccount = $transaction->getFrom();
							$table .="<td><a href='/viewAccount/".($fromAccount instanceof AccountEntry ? $transaction->getFrom()->getId(): "")."'>".($fromAccount instanceof AccountEntry ? $transaction->getFrom()->getName() : "")."</a></td>";
							$table .="<td><a href='/viewAccount/".$transaction->getTo()->getId()."'>".$transaction->getTo()->getName()."</a></td>";
							$table .="<td>$".self::getCurrency($transaction->getValue())."</td>";
							$table .="<td>".$transaction->getComments()."</td>";
						$table .="</tr>";
						$rowNo++;
						$total+=$transaction->getValue();
					}
					$table .="<tr style='background:#000000;color:#ffffff;height:34px;'>";
						$table .="<td>Total</td>";
						$table .="<td>&nbsp;</td>";
						$table .="<td>&nbsp;</td>";
						$table .="<td>$".self::getCurrency($total)."</td>";
						$table .="<td>&nbsp;</td>";
					$table .="</tr>";
			$table.="</table>";
		return $table;
	}
	
	public static function week_start_date($wk_num, $yr, $first = 1, $format = 'Y-m-d')
	{
	    $wk_ts  = strtotime('+' . $wk_num . ' weeks', strtotime($yr . '0101'));
	    $mon_ts = strtotime('-' . date('w', $wk_ts) + $first . ' days', $wk_ts);
	    return date($format, $mon_ts);
	} 
}
?>