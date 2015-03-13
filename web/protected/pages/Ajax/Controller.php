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
     * Getting an entity
     *
     * @param unknown $params
     *
     * @throws Exception
     * @return multitype:
     */
    private function _get($params)
    {
    	if(!isset($params['entityName']) || ($entityName = trim($params['entityName'])) === '')
    		throw new Exception('What are we going to get?');
    	if(!isset($params['entityId']) || ($entityId = trim($params['entityId'])) === '')
    		throw new Exception('What are we going to get with?');
    	return ($entity = $entityName::get($entityId)) instanceof BaseEntityAbstract ? $entity->getJson() : array();
    }
    /**
     * Getting All for entity
     *
     * @param unknown $params
     *
     * @throws Exception
     * @return multitype:multitype:
     */
    private function _getAll($params)
    {
    	if(!isset($params['entityName']) || ($entityName = trim($params['entityName'])) === '')
    		throw new Exception('What are we going to get?');
    	if(!isset($params['entityId']) || ($entityId = trim($params['entityId'])) === '')
    		throw new Exception('What are we going to get with?');
    	$searchTxt = trim(isset($params['searchTxt']) ? trim($params['searchTxt']) : '');
    	$searchParams = trim(isset($params['searchParams']) ? $params['searchParams'] : array());
    	$entityName = isset($params['entityName']) ? trim($params['entityName']) : '';
    	$pageNo = isset($params['pageNo']) ? trim($params['pageNo']) : null;
    	$pageSize = isset($params['pageSize']) ? trim($params['pageSize']) : DaoQuery::DEFAUTL_PAGE_SIZE;
    	$active = isset($params['active']) ? intval($params['active']) : null;
    	$orderBy = isset($params['orderBy']) ? trim($params['orderBy']) : array();

    	$stats = array();
    	$items = $entityName::getAllByCriteria($searchTxt, $searchParams, $active, $pageNo, $pageSize, $orderBy, $stats);
    	return array('items' => array_map(create_function('$a', 'return $a->getJson();'), $items), 'pagination' => $stats);
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
//     	$where = array('organizationId = ?');
//     	$param = array(Core::getOrganization()->getId());
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