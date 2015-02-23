<?php
/**
 * This is the transactions listing page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class Controller extends BackEndPageAbstract
{
	/**
	 * The menu item for the top menu
	 *
	 * @var string
	 */
	protected $_menuItem = 'transaction.list';
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= 'pageJs';
		$js .= '.setHTMLID("result-list-div", "result-wrapper")';
		$js .= '.setHTMLID("search-panel-div", "search-wrapper")';
		$js .= '.setHTMLID("search-btn", "search-btn")';
		$js .= '.setHTMLID("item-count", "item-count")';
		$js .= '.setCallbackId("getTransactions", "' . $this->getTransactionsBtn->getUniqueID() . '")';
		$js .= '.init()';
		$js .= ';';
		return $js;
	}
	/**
	 * Getting the transactions
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 */
	public function getTransactions($sender, $param)
	{
		$results = $errors = array ();
		try {
			$pageNo = 1;
			$pageSize = DaoQuery::DEFAUTL_PAGE_SIZE;
			if(isset($param->CallbackParameter->pagination)) {
				$pageNo = isset($param->CallbackParameter->pagination->pageNo) ? trim($param->CallbackParameter->pagination->pageNo) : $pageNo;
				$pageSize = isset($param->CallbackParameter->pagination->pageSize) ? trim($param->CallbackParameter->pagination->pageSize) : $pageSize;
			}

			$where = $params = array();
			if(isset($param->CallbackParameter->searchCriteria->accountsIds) && trim($param->CallbackParameter->searchCriteria->accountsIds) !== '') {
				$accountIds = explode(',', $param->CallbackParameter->accountsIds);
				$where[] = 'accountEntryId in (' . implode(',', array_fill(0, count($accountIds), '?') . ')');
				$params = array_merge($params, $accountIds);
			}
			if(isset($param->CallbackParameter->searchCriteria->logDate_from) && ($dateFrom = trim($param->CallbackParameter->searchCriteria->logDate_from)) !== '') {
				$where[] = 'logDate >= ?';
				$params[] = trim(new UDate($dateFrom));
			}
			if(isset($param->CallbackParameter->searchCriteria->logDate_to) && ($dateTo = trim($param->CallbackParameter->searchCriteria->logDate_to)) !== '') {
				$where[] = 'logDate <= ?';
				$params[] = trim(new UDate($dateTo));
			}
			var_dump($where);
			var_dump($params);

			$transactions = $stats = array();
			if(count($where) > 0)
				$transactions = Transaction::getAllByCriteria(implode(' AND ', $where), $params, true, $pageNo, $pageSize, array ('trans.id' => 'desc'), $stats );
			$results ['items'] = array_map ( create_function ( '$a', 'return $a->getJson();' ), $transactions );
			$results ['pagination'] = $stats;
		} catch ( Exception $ex ) {
			$errors [] = $ex->getMessage ();
		}
		$param->ResponseData = StringUtilsAbstract::getJson ( $results, $errors );
	}
}
?>