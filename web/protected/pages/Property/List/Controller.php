<?php
/**
 * This is the transactions listing page
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
	protected $_menuItem = 'property.list';
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= 'pageJs';
		$js .= '.setHTMLID("result-list-div", "result-wrapper")';
		$js .= '.setHTMLID("item-count", "item-count")';
		$js .= '.setCallbackId("getItems", "' . $this->getItemsBtn->getUniqueID() . '")';
		$js .= '.setCallbackId("delItems", "' . $this->delItemsBtn->getUniqueID() . '")';
		$js .= '.init()';
		$js .= ';';
		return $js;
	}
	/**
	 * Getting the Properties
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 */
	public function getItems($sender, $param)
	{
		$results = $errors = array ();
		try {
			$pageNo = 1;
			$pageSize = DaoQuery::DEFAUTL_PAGE_SIZE;
			if(isset($param->CallbackParameter->pagination)) {
				$pageNo = isset($param->CallbackParameter->pagination->pageNo) ? trim($param->CallbackParameter->pagination->pageNo) : $pageNo;
				$pageSize = isset($param->CallbackParameter->pagination->pageSize) ? trim($param->CallbackParameter->pagination->pageSize) : $pageSize;
			}

			$where = $params = array();
			$items = $stats = array();
			if(count($where) > 0)
				$items = Property::getAllByCriteria(implode(' AND ', $where), $params, true, $pageNo, $pageSize, array ('prop.name' => 'asc'), $stats);
			else
				$items = Property::getAll(true, $pageNo, $pageSize, array ('prop.name' => 'asc'), $stats);
			$results ['items'] = array_map ( create_function ( '$a', 'return $a->getJson();' ), $items );
			$results ['pagination'] = $stats;
		} catch ( Exception $ex ) {
			$errors [] = $ex->getMessage ();
		}
		$param->ResponseData = StringUtilsAbstract::getJson ( $results, $errors );
	}
	/**
	 * del Trans
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 */
	public function delTrans($sender, $param)
	{
		$results = $errors = array ();
		try {
			Dao::beginTransaction();

			if(!isset($param->CallbackParameter->id) || !($trans = Transaction::get(trim($param->CallbackParameter->id))) instanceof Transaction)
				throw new Exception('System Error: can NOT find the transaction that you are trying to delete.');
			$trans->setActive(false)
				->save();
			$results ['item'] = $trans->getJson();
			Dao::commitTransaction();
		} catch ( Exception $ex ) {
			Dao::rollbackTransaction();
			$errors [] = $ex->getMessage ();
		}
		$param->ResponseData = StringUtilsAbstract::getJson ( $results, $errors );
	}
}
?>