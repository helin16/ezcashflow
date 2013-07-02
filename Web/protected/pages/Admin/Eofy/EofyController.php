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
	/**
	 * Generating the excel report
	 * 
	 * @param TButton            $sender The button when clicked
	 * @param TCallbackParameter $param  The parameter when clicked fired
	 */
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
					echo "<th>Acc Type</th>";
					echo "<th>Account</th>";
					echo "<th>Value</th>";
					echo "<th>Date</th>";
					echo "<th>Comments</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			foreach($this->_getTrans($fromDate, $toDate) as $path => $trans)
			{
				echo "<tr>";
					echo "<td>" . array_shift($trans['path']). "</td>";
					echo "<td>" . implode(' / ', $trans['path']). "</td>";
					echo "<td>" . $trans['value'] . "</td>";
					echo "<td>" . $trans['created'] . "</td>";
					echo "<td>" . $trans['comments'] . "</td>";
				echo "</tr>";
			}
			echo "</tbody>";
		echo "</table>";
		die;
	}
	/**
	 * Getting the transactions for the selected dates
	 * 
	 * @param string $fromDate The from date
	 * @param string $toDate   The to date
	 * 
	 * @return array
	 */
	private function _getTrans($fromDate, $toDate)
	{
		$transArray = array();
		foreach(BaseService::getInstance('TransactionService')->getTransBetweenDates($fromDate, $toDate, array(AccountEntry::TYPE_INCOME, AccountEntry::TYPE_EXPENSE)) as $trans)
		{
			$transArray[] = array('path' => explode(' / ', $trans->getTo()->getBreadCrumbs()), 'created' => $trans->getCreated(), 'value' => $trans->getValue(), 'comments' => $trans->getComments());
		}
		ksort($transArray);
		return $transArray;
	}
}
?>