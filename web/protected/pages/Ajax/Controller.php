<?php
/**
 * This is the Ajax Service
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 *
 */
class Controller extends TService
{
    /**
     * (non-PHPdoc)
     * @see TService::run()
     */
    public function run()
    {
    	$results = $errors = array();
        try
        {
            $method = '_' . ((isset($this->Request['method']) && trim($this->Request['method']) !== '') ? trim($this->Request['method']) : '');
            if(!method_exists($this, $method))
                throw new Exception('No such a method: ' . $method . '!');

            $results = $this->$method($_REQUEST);
        }
        catch (Exception $ex)
        {
        	$errors = $ex->getMessage() . $ex->getTraceAsString();
        }
        $this->getResponse()->flush();
        $this->getResponse()->appendHeader('Content-Type: application/json');
        $this->getResponse()->write(StringUtilsAbstract::getJson($results, $errors));
    }
    /**
     * Getting the accounts
     * @param unknown $params
     * @return multitype:multitype:
     */
    private function _getAccounts($params)
    {
    	$searchTxt = trim(isset($params['searchTxt']) ? trim($params['searchTxt']) : '');
    	$pageNo = isset($params['pageNo']) ? trim($params['pageNo']) : null;
    	$pageSize = isset($params['pageSize']) ? trim($params['pageSize']) : DaoQuery::DEFAUTL_PAGE_SIZE;
    	$orderBy = isset($params['orderBy']) ? trim($params['orderBy']) : array();
    	$isSumAcc = isset($params['isSumAcc']) ? intval($params['isSumAcc']) : null;
    	$active = isset($params['active']) ? intval($params['active']) : null;
    	$accTypeIds = isset($params['accTypeIds']) ? (is_string($params['accTypeIds']) ? explode(',', trim($params['accTypeIds'])) : $params['accTypeIds']) : array();

    	$where = array();
    	$param = array();
    	if(trim($searchTxt) !== '') {
	    	$where[] = '(name like :searchTxt or accountNo like :searchTxt)';
	    	$param['searchTxt'] = '%' . trim($searchTxt) . '%';
    	}
		if($isSumAcc !== null) {
			$where[] = 'isSumAcc = :isSumAcc';
			$param['isSumAcc'] = $isSumAcc;
		}
		if($active !== null) {
			$where[] = 'active = :active';
			$param['active'] = $active;
		}
		if(count($accTypeIds) > 0) {
	    	$accTypeString = array();
	    	foreach($accTypeIds as $index => $accTypeId) {
	    		$key = "accTypeId" . $index;
	    		$accTypeString[] = ':' . $key;
	    		$param[$key] = trim($accTypeId);
	    	}
    		$where[]= 'typeId in (' . implode(', ', $accTypeString) . ')';
		}
    	$stats = array();
    	$accounts = array();
    	if(count($where) > 0)
    		$accounts = AccountEntry::getAllByCriteria(implode(' AND ', $where), $param, false, $pageNo, $pageSize, $orderBy, $stats);
    	else
    		$accounts = AccountEntry::getAll(false, $pageNo, $pageSize, $orderBy, $stats);
    	return array('items' => array_map(create_function('$a', 'return $a->getJson();'), $accounts), 'pagination' => $stats);
    }
}