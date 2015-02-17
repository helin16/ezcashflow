<?php
/**
 * This is the home page
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
	protected $_menuItem = 'home';
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs ();
		$js .= 'pageJs';
		$js .= '.setCallbackId("getLatestTrans", "' . $this->getLatestTransBtn->getUniqueID () . '")';
		$js .= '.setCallbackId("getSummary", "' . $this->getSummaryBtn->getUniqueID () . '")';
		$js .= '.init("page-wrapper", "#' . $this->getForm ()->getClientID () . '");';
		return $js;
	}
	/**
	 * Getting the latest tranactions
	 *
	 * @param unknown $sender
	 * @param unknown $params
	 *
	 */
	public function getLatestTrans($sender, $params)
	{
		$results = $errors = array ();
		try {
			$transactions = Transaction::getAll ( true, 1, 10, array (
					'trans.id' => 'desc'
			) );
			$results ['items'] = array_map ( create_function ( '$a', 'return $a->getJson();' ), $transactions );
		} catch ( Exception $ex ) {
			$errors [] = $ex->getMessage ();
		}
		$params->ResponseData = StringUtilsAbstract::getJson ( $results, $errors );
	}
	private function _getTransSum(AccountType $type, UDate $fromUTC, UDate $toUTC)
	{
		Transaction::getQuery ()->eagerLoad ( 'Transaction.accountEntry', 'inner join', 'trans_acc', 'trans_acc.id = trans.accountEntryId and trans_acc.typeId = ?' );
		$transactions = Transaction::getAllByCriteria ( 'trans.created >=? and trans.created <= ?', array (
				$type->getId (),
				trim ( $fromUTC ),
				trim ( $toUTC )
		) );
		$sum = 0;
		foreach ( $transactions as $trans ) {
			$sum += $trans->getValue ();
		}
		return $sum;
	}
	private function _getDateRangeData($params, $string)
	{
		if (! isset ( $params->$string ) || ($dates = $params->$string) === null)
			throw new Exception ( 'No date range for ' . $string . ' is provided!' );
		$from = new UDate ( $dates->from );
		$to = new UDate ( $dates->to );
		$data = array ();
		$data ['income'] = $this->_getTransSum ( AccountType::get ( AccountType::ID_INCOME ), $from, $to );
		$data ['expense'] = $this->_getTransSum ( AccountType::get ( AccountType::ID_EXPENSE ), $from, $to );
		return $data;
	}
	public function getSummary($sender, $params)
	{
		$results = $errors = array ();
		try {

			$results ['items'] = array ();
			$results ['items'] ['today'] = $this->_getDateRangeData ( $params->CallbackParameter, 'today' );
			$results ['items'] ['week'] = $this->_getDateRangeData ( $params->CallbackParameter, 'week' );
			$results ['items'] ['month'] = $this->_getDateRangeData ( $params->CallbackParameter, 'month' );
			$results ['items'] ['year'] = $this->_getDateRangeData ( $params->CallbackParameter, 'year' );
			$results ['items'] ['total'] = $this->_getDateRangeData ( $params->CallbackParameter, 'total' );
		} catch ( Exception $ex ) {
			$errors [] = $ex->getMessage ();
		}
		$params->ResponseData = StringUtilsAbstract::getJson ( $results, $errors );
	}
}
?>
