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
		$types = array();
		foreach(AccountType::getAll() as $type)
			$types[] = $type->getJson();
		$js = parent::_getEndJs();
		$js .= 'pageJs';
		$js .= '.setHTMLID("result-list-div", "result-wrapper")';
		$js .= '.setHTMLID("search-panel-div", "search-wrapper")';
		$js .= '.setHTMLID("search-btn", "search-btn")';
		$js .= '.setHTMLID("item-count", "item-count")';
		$js .= '.setCallbackId("getTransactions", "' . $this->getTransactionsBtn->getUniqueID() . '")';
		$js .= '.setCallbackId("delTrans", "' . $this->delTransBtn->getUniqueID() . '")';
		$js .= '.setAccountTypes(' . json_encode($types) . ')';
		$preSetData = array();
		if(isset($_REQUEST['accountids']) || isset($_REQUEST['localFromDate']) || isset($_REQUEST['localToDate']) || isset($_REQUEST['typeId']) || isset($_REQUEST['lookDownAccId'])) {
			if(isset($_REQUEST['accountids'])) {
				$accounts = array();
				$accountIds = explode(',', $_REQUEST['accountids']);
				$accountIds = array_filter($accountIds);
				if(count($accountIds) > 0)
					$accounts = AccountEntry::getAllByCriteria('id in (' . implode(', ', array_fill(0, count($accountIds), '?')) . ')', $accountIds);
				$preSetData['accounts'] = AccountEntry::translateToJson($accounts);
			} else if (isset($_REQUEST['lookDownAccId'])) {
				if(!($lookDownAcc = AccountEntry::get($_REQUEST['lookDownAccId'])) instanceof AccountEntry)
					throw new Exception('Invalid look down account id:' . $_REQUEST['lookDownAccId']);
				$accounts = array($lookDownAcc);
				$preSetData['accounts'] = AccountEntry::translateToJson(array_merge($accounts, $lookDownAcc->getChildren(true)));
			}
			if(isset($_REQUEST['localFromDate'])) {
				$localFromDate = new UDate(trim($_REQUEST['localFromDate']));
				$preSetData['localFromDate'] = $localFromDate->format('d/M/Y h:i A');
			}
			if(isset($_REQUEST['localToDate'])) {
				$localToDate = new UDate(trim($_REQUEST['localToDate']));
				$preSetData['localToDate'] = $localToDate->format('d/M/Y h:i A');
			}
			if(isset($_REQUEST['typeId'])) {
				$preSetData['typeId'] = trim($_REQUEST['typeId']);
			}
		}
		if(count($preSetData) > 0)
			$js .= '._setPreData(' . json_encode($preSetData). ')';
		$js .= '.init()';
		$js .= ';';
		if(count($preSetData) > 0) {
			$js .= '$("search-btn").click()';
		}
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

			$where = array('trans.organizationId = :orgId');
			$params = array('orgId' => trim(Core::getOrganization()->getId()));
			if(isset($param->CallbackParameter->searchCriteria->accountsIds) && trim($param->CallbackParameter->searchCriteria->accountsIds) !== '') {
				$accountIds = explode(',', $param->CallbackParameter->searchCriteria->accountsIds);
				if(($accounts = AccountEntry::getAllByCriteria('id in (' . implode(',', array_fill(0, count($accountIds), '?')) . ')', $accountIds)) > 0) {
					$array = array();
					foreach($accounts as $index => $account) {
						$key = 'path' . $index;
						$array[] = '(trans_acc.id = ' . $account->getId() . ' or trans_acc.path like :' . $key . ')';
						$params[$key] = trim($account->getPath() . ',%');
					}
					$where[] = '(' . implode(' OR ', $array) . ')';
				}
			}
			if(isset($param->CallbackParameter->searchCriteria->logDate_from) && ($dateFrom = trim($param->CallbackParameter->searchCriteria->logDate_from)) !== '') {
				$where[] = 'trans.logDate >= :fromDate';
				$params['fromDate'] = trim(new UDate($dateFrom));
			}
			if(isset($param->CallbackParameter->searchCriteria->logDate_to) && ($dateTo = trim($param->CallbackParameter->searchCriteria->logDate_to)) !== '') {
				$where[] = 'trans.logDate <= :toDate';
				$params['toDate'] = trim(new UDate($dateTo));
			}
			if(isset($param->CallbackParameter->searchCriteria->accountTypeId) && ($accountTypeId = trim($param->CallbackParameter->searchCriteria->accountTypeId)) !== '') {
				$where[] = 'trans_acc.typeId = :typeId';
				$params['typeId'] = trim($accountTypeId);
			}

			$transactions = $stats = array();
			if(count($where) > 0)
			{
				Transaction::getQuery()->eagerLoad('Transaction.accountEntry', 'inner join', 'trans_acc', 'trans_acc.id = trans.accountEntryId');
				$transactions = Transaction::getAllByCriteria(implode(' AND ', $where), $params, true, $pageNo, $pageSize, array ('trans.logDate' => 'desc'), $stats );
			}
			$results ['items'] = Transaction::translateToJson($transactions );
			$results ['pagination'] = $stats;
		} catch ( Exception $ex ) {
			$errors [] = $ex->getMessage ();
		}
		$param->ResponseData = StringUtilsAbstract::getJson ( $results, $errors );
	}
	/**
	 * del Trans
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 */
	public function delTrans($sender, $param)
	{
		$results = $errors = array ();
		try {
			Dao::beginTransaction();

			if(!isset($param->CallbackParameter->id) || !($trans = Transaction::get(trim($param->CallbackParameter->id))) instanceof Transaction)
				throw new Exception('System Error: can NOT find the transaction that you are trying to delete.');
			$trans->setActive(false)
				->save();
			$results ['item'] = $trans->getJson();
			Dao::commitTransaction();
		} catch ( Exception $ex ) {
			Dao::rollbackTransaction();
			$errors [] = $ex->getMessage ();
		}
		$param->ResponseData = StringUtilsAbstract::getJson ( $results, $errors );
	}
}
?>