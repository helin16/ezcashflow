<?php
/**
 * This is the Property::Details page
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
		if(!isset($this->Request['id']) || ($id = trim($this->Request['id'])) === '')
			return null;

		if($id === 'new')
			$entity = new Property();
		else
			$entity = Property::get($id);
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
			if(!isset($params->CallbackParameter->name) || ($name = trim($params->CallbackParameter->name)) === '')
				throw new Exception('No name provided.');
			$setupAcc = (isset($params->CallbackParameter->setupAccId) ? AccountEntry::get($params->CallbackParameter->setupAccId) : null);
			$incomeAcc = (isset($params->CallbackParameter->incomeAccId) ? AccountEntry::get($params->CallbackParameter->incomeAccId) : null);
			$expenseAcc = (isset($params->CallbackParameter->expenseAccId) ? AccountEntry::get($params->CallbackParameter->expenseAccId) : null);
			$boughtPrice = (isset($params->CallbackParameter->boughtPrice) ? trim($params->CallbackParameter->boughtPrice) : 0);
			$description = (isset($params->CallbackParameter->description) ? trim($params->CallbackParameter->description) : '');

			if(isset($params->CallbackParameter->propertyId)) {
				$propertyId = trim($params->CallbackParameter->propertyId);
				if(!($property = Property::get($propertyId)) instanceof Property)
					throw new Exception('Invalid Property: ' . $propertyId);
				$property->setDescription($description)
					->setSetupAcc($setupAcc)
					->setIncomeAcc($incomeAcc)
					->setExpenseAcc($expenseAcc)
					->setName($name)
					->setBoughtPrice($boughtPrice)
					->save();
			} else {
				$property = Property::create(Core::getOrganization(), $name, $description, $boughtPrice, $setupAcc, $incomeAcc, $expenseAcc);
			}
			//if there is attachments
			if(isset($params->CallbackParameter->files) && count($files = $params->CallbackParameter->files)  > 0) {
				foreach($files as $file) {
					if(isset($file->id)) {
						if(!($attachment = Attachment::get($file->id)) instanceof Attachment)
							throw new Exception('Invalid attachment: ID=' . $file->id);
						if(isset($file->active) && intval($file->active) === 0)
							$attachment->setActive(false)->save();
					} else {
						$asset = Asset::registerAsset($file->file->name, $file->file->path);
						$property->addAttachment($asset);
					}
				}
			}
			$results['item'] = $property->getJson();
			Dao::commitTransaction();

		} catch(Exception $ex) {
			Dao::rollbackTransaction();
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}
?>