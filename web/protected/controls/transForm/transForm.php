<?php
class transForm extends TTemplateControl
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		if(!$this->getPage()->IsPostBack || !$this->getPage()->IsCallback)
		{
			$clientScript = $this->getPage()->getClientScript();
			$clientScript->registerPradoScript('ajax');
			$clientScript->registerScriptFile('transForm.js', $this->publishAsset(get_class($this) . '.js'));
			$clientScript->registerBeginScript('transForm.init.js', 'TransFormJs.searchCallbackId="' . $this->searchAccountsBtn->getUniqueID() . '";TransFormJs.saveTransCallbackId="' . $this->saveTransBtn->getUniqueID() . '";');
		}
	}

	public function searchAccounts($sender, $params)
	{
		$results = $errors = array();
		try {
			if(!isset($params->CallbackParameter->searchTxt) || ($searchTxt = trim($params->CallbackParameter->searchTxt)) === '')
				throw new Exception('Please provide some word to search for the account entry.');
			if(!isset($params->CallbackParameter->accTypeIds) || count($accTypeIds = ($params->CallbackParameter->accTypeIds)) === 0)
				throw new Exception('System Error: no Account Types passed in.');
			$accTypeString = array();
			$param = array('searchTxt' => '%' . $searchTxt . '%');
			$where = 'isSumAcc = 0 and (name like :searchTxt or accountNo like :searchTxt)';
			foreach($accTypeIds as $index => $accTypeId) {
				$key = "accTypeId" . $index;
				$accTypeString[] = ':' . $key;
				$param[$key] = $accTypeId;
			}
			if(count($accTypeString) > 0) {
				$where .= ' AND typeId in (' . implode(', ', $accTypeString) . ')';
			}
			$accounts = AccountEntry::getAllByCriteria($where, $param);
			$results['items'] = array_map(create_function('$a', 'return $a->getJson();'), $accounts);
		} catch(Exception $ex) {
			$errors[] = $ex->getMessage();
		}
		header('Content-Type: application/json');
		echo StringUtilsAbstract::getJson($results, $errors);
		die();
	}

	public function saveTrans($sender, $params)
	{
		$results = $errors = array();
		try {
			Dao::beginTransaction();
			if(!isset($params->CallbackParameter->fromAccId) || !($fromAcc = AccountEntry::get($params->CallbackParameter->fromAccId)) instanceof AccountEntry || intval($fromAcc->getIsSumAcc()) === 1)
				throw new Exception('From Account is invalid!');
			if(!isset($params->CallbackParameter->fromAccId) || !($toAcc = AccountEntry::get($params->CallbackParameter->toAccId)) instanceof AccountEntry || intval($toAcc->getIsSumAcc()) === 1)
				throw new Exception('To Account is invalid!');
			if(!isset($params->CallbackParameter->logDate) || !($logDate = new UDate(trim($params->CallbackParameter->logDate))) instanceof UDate || trim($logDate) === trim(UDate::zeroDate()))
				$logDate = new UDate();
			if($fromAcc->getId() === $toAcc->getId())
				throw new Exception('Can NOT make a transaction between the same account!');
			if(!isset($params->CallbackParameter->amount) || !($amount = trim($params->CallbackParameter->amount)) === '')
				throw new Exception('The amount is invalid!');
			$comments = "";
			if(isset($params->CallbackParameter->comments) )
				$comments = trim($params->CallbackParameter->comments);
			$transactions = array(
				Transaction::create($fromAcc, $logDate, $amount, null, $comments),
				Transaction::create($toAcc, $logDate, null, $amount, $comments)
			);
			//if there is attachments
			if(isset($params->CallbackParameter->files) && count($files = $params->CallbackParameter->files)  > 0) {
				foreach($files as $file) {
					$asset = Asset::registerAsset($file->fileName, $file->filePath);
					foreach($transactions as $transaction) {
						$transaction->addAttachment($asset);
					}
				}
			}
			Dao::commitTransaction();
		} catch(Exception $ex) {
			Dao::rollbackTransaction();
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}