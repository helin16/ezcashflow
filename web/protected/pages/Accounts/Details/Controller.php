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
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
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
				$entity->setParent($parent);
			else if($type instanceof AccountType)
				$entity->setType($type);
			else
				$entity = null;
		}
		else
			$entity = AccountEntry::get($id);
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
	public function saveItem($sender, $param)
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