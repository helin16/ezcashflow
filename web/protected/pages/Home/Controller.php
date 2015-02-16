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
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= 'pageJs';
		$js .= '.setCallbackId("getLatestTrans", "' . $this->getLatestTransBtn->getUniqueID() . '")';
		$js .= '.init("page-wrapper", "#' . $this->getForm()->getClientID() . '");';
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
		$results = $errors = array();
		try {
			$transactions = Transaction::getAll(true, 1, 10, array('trans.id' => 'desc'));
			$results['items'] = array_map(create_function('$a', 'return $a->getJson();'), $transactions);
		} catch(Exception $ex) {
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}
?>