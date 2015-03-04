<?php
/**
 * This is the Accounts::Details page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class Controller extends DetailsPageAbstract
{
	private $_entity;
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		if($this->_entity instanceof BaseEntityAbstract)
			$js .= 'pageJs.init();';
		return $js;
	}
	/**
	 * (non-PHPdoc)
	 * @see DetailsPageAbstract::_getEntity()
	 */
	protected function _getEntity()
	{
		$entity = $parent = $type = null;

		if(!isset($this->Request['id']) || ($id = trim($this->Request['id'])) === '')
			return $entity;

		if($id === 'new' && (isset($_REQUEST['parentId']) && ($parent = AccountEntry::get($_REQUEST['parentId'])) || isset($_REQUEST['typeId']) && ($type = AccountType::get($_REQUEST['typeId'])))) {
			$entity = new AccountEntry();
			if($parent instanceof AccountEntry)
				$entity->setParent($parent)->setType($parent->getType());
			else if($type instanceof AccountType)
				$entity->setType($type);
			else
				$entity = null;
		}
		else
			$entity = AccountEntry::get($id);
		$this->_entity = $entity;
		return $entity;
	}
	/**
	 * save the items
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 * @throws Exception
	 *
	 */
	public function saveItem($sender, $params)
	{
		$results = $errors = array();
		try {
			Dao::beginTransaction();
			if(!isset($params->CallbackParameter->typeId) || !($type = AccountType::get(trim($params->CallbackParameter->typeId))) instanceof AccountType)
				throw new Exception('No typeId provided.');
			if(!isset($params->CallbackParameter->name) || ($name = trim($params->CallbackParameter->name)) === '')
				throw new Exception('No name provided.');
			if(!isset($params->CallbackParameter->accountNo) || ($accountNo = trim($params->CallbackParameter->accountNo)) === '')
				throw new Exception('No accountNo provided.');
			$isSumAcc = false;
			if(isset($params->CallbackParameter->isSumAcc))
				$isSumAcc = intval($params->CallbackParameter->isSumAcc) === 1 ? true : false;
			$parent = null;
			if(isset($params->CallbackParameter->parentId) && !($parent = AccountEntry::get(trim($params->CallbackParameter->parentId))) instanceof AccountEntry)
				throw new Exception('Invalid parent account provided.');
			$initValue = 0;
			if(isset($params->CallbackParameter->initValue) && !is_numeric($initValue = trim($params->CallbackParameter->initValue)))
				throw new Exception('Invalid initial value provided.');
			$description = '';
			if(isset($params->CallbackParameter->description))
				$description = trim($params->CallbackParameter->description);

			if(isset($params->CallbackParameter->accId)) {
				$accountId = trim($params->CallbackParameter->accId);
				if(!($account = AccountEntry::get($accountId)) instanceof AccountEntry)
					throw new Exception('Invalid Account: ' . $accountId);
				$account->setDescription($description)
					->setAccountNo($accountNo)
					->setIsSumAcc($isSumAcc)
					->setName($name)
					->setInitValue($initValue)
					->save();
			}
			else if($parent instanceof AccountEntry)
				$account = AccountEntry::create(Core::getOrganization(), $parent, $name, $isSumAcc, $initValue, $description, $accountNo);
			else
				$account = AccountEntry::createRootAccount(Core::getOrganization(), $name, $type, $isSumAcc, $initValue, $description, $accountNo);
			$results['item'] = $account->getJson();
			Dao::commitTransaction();

		} catch(Exception $ex) {
			Dao::rollbackTransaction();
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}
?>