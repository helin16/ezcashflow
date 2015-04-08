<?php
/**
 * This is the transactions details page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class Controller extends DetailsPageAbstract
{
	private $_entity;
	/**
	 * The menu item for the top menu
	 *
	 * @var string
	 */
	protected $_menuItem = 'transaction.details';
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		if($this->_entity instanceof Transaction){
			$transactions = Transaction::getAllByCriteria('groupId = ?', array(trim($this->_entity->getGroupId())), true, 1, 2, array('trans.debit' => 'asc'));
			$fromAcc = $transactions[0]->getAccountEntry()->getJson();
			$toAcc = $transactions[1]->getAccountEntry()->getJson();
			$js .= 'pageJs';
				$js .= '._setFromNToAcc(' . json_encode($fromAcc) . ', ' . json_encode($toAcc) . ')';
				$js .= '.init();';
		}
		return $js;
	}
	/**
	 * (non-PHPdoc)
	 * @see DetailsPageAbstract::_getEntity()
	 */
	protected function _getEntity()
	{
		if(!isset($this->Request['id']) || ($id = trim($this->Request['id'])) === '' || !($entity = Transaction::get($id)) instanceof Transaction)
			return null;
		$this->_entity = $entity;
		return $entity;
	}
}
?>