<?php
/**
 * This is the End Of Financial Year Report page
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class EofyController extends PageAbstract  
{
	public function genReport($sender, $param)
	{
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=ProfitsAndLost.xls");  //File name extension was wrong
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		
		$fromDate = trim($this->fromDate->Text);
		$toDate = trim($this->toDate->Text);
		echo "<table>";
			echo"<thead>";
				echo "<tr>";
					echo "<th>Account</th>";
					echo "<th>Value</th>";
					echo "<th>Date</th>";
					echo "<th>Comments</th>";
				echo "<tr>";
			echo "</thead>";
			echo "<tbody>";
			$trans = BaseService::getInstance('TransactionService')->getTransBetweenDates($fromDate, $toDate, array(AccountEntry::TYPE_INCOME, AccountEntry::TYPE_EXPENSE), null, DaoQuery::DEFAUTL_PAGE_SIZE, array('to.accountNumber' => 'asc', 'trans.created' => 'asc'));
			foreach($trans as $trans)
			{
				echo "<tr>";
					echo "<td>" . $trans->getTo()->getBreadCrumbs(). "</td>";
					echo "<td>" . $trans->getTo()->getAccountNumber(). "</td>";
					echo "<td>" . $trans->getValue() . "</td>";
					echo "<td>" . $trans->getCreated() . "</td>";
					echo "<td>" . $trans->getComments() . "</td>";
				echo "<tr>";
			}
			echo "</tbody>";
		echo "</table>";
		die;
	}
}
?>