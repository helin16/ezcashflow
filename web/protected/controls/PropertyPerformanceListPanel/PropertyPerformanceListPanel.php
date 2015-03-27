<?php
class PropertyPerformanceListPanel extends TTemplateControl
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onInit()
	 */
	public function onInit($param)
	{
		parent::onInit($param);
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
		if(!$this->getPage()->IsCallBack && !$this->getPage()->IsPostBack) {
			$js = 'if(typeof(PropertyPerformanceListPanelJs) !== "undefined") {';
			$js .= 'PropertyPerformanceListPanelJs.callbackIds = ' . json_encode(array(
					'getData' => $this->getDataBtn->getUniqueID()
			)) . ';';
			$js .= '}';
			$this->getPage()->getClientScript()->registerEndScript('plpJs', $js);
		}
	}

	/**
	 * Getting the data
	 *
	 * @param unknown $sender
	 * @param unknown $params
	 *
	 * @throws Exception
	 */
	public function getData($sender, $params)
	{
		$results = $errors = array();
		try {
			if(!isset($params->CallbackParameter->propertyId) || !($property = Property::get($params->CallbackParameter->propertyId)) instanceof Property)
				throw new Exception('System Error: invalid property provided.');
			if(!isset($params->CallbackParameter->dateRange))
				throw new Exception('System Error: no date range provided.');
			$data = array();
			foreach($params->CallbackParameter->dateRange as $dateRange) {
				$data[] = array(
					'startTime' => trim($startTime = new UDate($dateRange->startTime))
					,'endTime' => trim($endTime = new UDate($dateRange->endTime))
					,'income' => ($property->getIncomeAcc() instanceof AccountEntry ? $property->getIncomeAcc()->getPeriodRuningBalance($startTime, $endTime, true) : '')
					,'expense' => ($property->getExpenseAcc() instanceof AccountEntry ? $property->getExpenseAcc()->getPeriodRuningBalance($startTime, $endTime, true) : '')
				);
			}
			$results['items'] = $data;

		} catch(Exception $ex) {
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}