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
		$types = array_map(create_function('$a', 'return $a->getJson();'), AccountType::getAll());
		$js .= 'pageJs.setCallbackId("getAccounts", "' . $this->getAccountsBtn->getUniqueID() . '")';
		$js .= '.init("page-wrapper", ' . json_encode($types) . ');';
		return $js;
	}
	public function getAccounts($sender, $params)
	{
		$results = $errors = array();
		try {
			if(!isset($params->CallbackParameter->typeId))
				throw new Exception('No typeId provided.');

		} catch(Exception $ex) {
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}
?>