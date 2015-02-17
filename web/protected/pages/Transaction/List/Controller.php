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
		$js = 'pageJs';
		$js = '.setHTMLID("result-list-div", "result-wrapper")';
		$js = '.setHTMLID("search-panel-div", "result-wrapper")';
		$js = '.setHTMLID("search-btn", "search-btn")';
		$js = '.setCallbackId("getTransactions", "' . $this->getTransactionsBtn->getUniqueID() . '")';
		$js = ';';
		return $js;
	}
}
?>