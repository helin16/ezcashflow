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
			$clientScript->registerBeginScript('transForm.init.js', 'TransFormJs.searchCallbackId="' . $this->searchAccountsBtn->getUniqueID() . '";');
		}
	}

	public function searchAccounts($sender, $params)
	{
		$results = $errors = array();
		try {
			if(!isset($params->CallbackParameter->searchTxt) || ($searchTxt = trim($params->CallbackParameter->searchTxt)) === '')
				throw new Exception('Please provide some word to search for the account entry.');
			$accounts = AccountEntry::getAllByCriteria('name like :searchTxt or accountNo like :searchTxt', array('searchTxt' => '%' . $searchTxt . '%'));
			$results['items'] = array_map(create_function('$a', 'return $a->getJson();'), $accounts);
		} catch(Exception $ex) {
			$errors[] = $ex->getMessage();
		}
		header('Content-Type: application/json');
		echo StringUtilsAbstract::getJson($results, $errors);
		die();
	}
}