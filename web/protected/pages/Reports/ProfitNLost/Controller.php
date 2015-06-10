<?php
/**
 * This is the reports page
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

			$where = array('organizationId = ?');
			$params = array(trim(Core::getOrganization()->getId()));
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
}
?>