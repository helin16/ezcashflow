<?php
class transForm extends TTemplateControl
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		parent::onInit($param);
		if(!$this->getPage()->IsPostBack || !$this->getPage()->IsCallback)
		{
			$clientScript = $this->getPage()->getClientScript();
			$clientScript->registerPradoScript('ajax');
			$className = get_class($this);
			$scriptArray = FrontEndPageAbstract::getLastestJS($className);
			foreach($scriptArray as $key => $value) {
				if(($value = trim($value)) !== '') {
					if($key === 'js')
						$this->getPage()->getClientScript()->registerScriptFile($className . 'Js', $this->publishAsset($value));
					else if($key === 'css')
						$this->getPage()->getClientScript()->registerStyleSheetFile($className . 'Css', $this->publishAsset($value));
				}
			}
			$clientScript->registerBeginScript('transForm.init.js', 'TransFormJs.searchCallbackId="' . $this->searchAccountsBtn->getUniqueID() . '";TransFormJs.saveTransCallbackId="' . $this->saveTransBtn->getUniqueID() . '";');
		}
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
					$asset = Asset::registerAsset($file->file->name, $file->file->path);
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