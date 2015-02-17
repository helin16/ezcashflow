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
			$transactions = Transaction::getAll ( true, 1, 10, array ('trans.id' => 'desc') );
			$results ['items'] = array_map ( create_function ( '$a', 'return $a->getJson();' ), $transactions );
		} catch ( Exception $ex ) {
			$errors [] = $ex->getMessage ();
		}
		$param->ResponseData = StringUtilsAbstract::getJson ( $results, $errors );
	}
}
?>