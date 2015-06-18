<?php
/**
 * This is the account entry listing page
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
	protected $_menuItem = 'accountentry.list';
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$types = array();
		foreach(AccountType::getAll() as $a)
			$types[] = $a->getJson();;
		$js .= 'pageJs.setCallbackId("getAccounts", "' . $this->getAccountsBtn->getUniqueID() . '")';
		$js .= '.setCallbackId("deleteAccount", "' . $this->deleteAccountBtn->getUniqueID() . '")';
		$js .= '.init("page-wrapper", ' . json_encode($types);
		if(isset($_REQUEST['typeid']) && ($firstShowAccType = AccountType::get($_REQUEST['typeid'])) instanceof AccountType)
			$js .= ', ' . json_encode($firstShowAccType->getJson());
		$js .= ');';
		return $js;
	}
	public function getAccounts($sender, $params)
	{
		$results = $errors = array();
		try {
			if(!isset($params->CallbackParameter->typeId) || ($typeId = trim($params->CallbackParameter->typeId)) === '')
				throw new Exception('No typeId provided.');
			$accounts = AccountEntry::getAllByCriteria('typeId = ? and organizationId = ?', array($typeId, Core::getOrganization()->getId()), true, null, DaoQuery::DEFAUTL_PAGE_SIZE, array('acc_entry.path' => 'asc'));
			$results['items'] = array();
			foreach($accounts as $acc)
				$results['items'][] = $acc->getJson();
		} catch(Exception $ex) {
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
	public function deleteAccount($sender, $params)
	{
		$results = $errors = array();
		try {
			if(!isset($params->CallbackParameter->accId) || !($account = AccountEntry::get(trim($params->CallbackParameter->accId))) instanceof AccountEntry)
				throw new Exception('Invalid Account to delete');
			$account->setActive(false)
				->save();
			$results['item'] = $account->getJson();
		} catch(Exception $ex) {
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}
?>