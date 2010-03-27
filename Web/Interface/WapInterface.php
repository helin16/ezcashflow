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
			$wapService = new WapUserService();
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
									<div style='padding:15px;'>
										<fieldset>
											<legend>Spend Money</legend>
											<form action='/post/WapUserService/spendMoney' method='POST'>
												<table width=\"100%\" style='background:#cccccc;'>
													<tr>
														<td width='100px'>
															From:
														</td>
														<td>
															".$wapService->getDropDownListForAccounts("fromAccountId",1)."
														</td>
													</tr>
													<tr>
														<td width='100px'>
															To:
														</td>
														<td>
															".$wapService->getDropDownListForAccounts("toAccountId",4)."
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
															Comments:
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
											<legend>Earn Money</legend>
											<form action='/post/WapUserService/earnMoney' method='POST'>
												<table width=\"100%\" style='background:#cccccc;'>
													<tr>
														<td>
															Into:
														</td>
														<td>
															".$wapService->getDropDownListForAccounts("toAccountId",3)."
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
															Comments:
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
							&nbsp;&nbsp; | &nbsp;&nbsp; <a href='/addAccount/".$entry->getId()."'>Add New Account Under '".$entry->getName()."'</a>
							".self::getViewAccountTable($accountId,$entry->getName(),$entry->getAccountNumber(),$entry->getValue(),$entry->getComments())."
							<a href='/manageAccounts/'>Back</a>
							&nbsp;&nbsp; | &nbsp;&nbsp; <a href='/addAccount/".$entry->getId()."'>Add New Account Under '".$entry->getName()."'</a>
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
		return "<table width=\"100%\">
					<tr>
						<td>
							".self::getMenu(2)."
						</td>
					</tr>
					<tr>
						<td>
							Under construction!
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
					<li><a href='/manageAccounts/' ".self::showHightlight($i++,$heightlightIndex).">Manage Accounts</a></li>
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
				<a href='/manageAccounts/'>Bank</a>&nbsp;&nbsp;| &nbsp;&nbsp;
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
							$ ".self::getCurrency($row["value"])."
						</td>
						<td width='5%'>
							".(
									(
										$row['countChildren']==0 
										&& $row['parentId']!=NULL
									) ? "<a href='/deleteAccount/".$row["id"]."'> Delete </a>" : ""
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
}
?>