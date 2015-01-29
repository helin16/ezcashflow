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
        	$errors = $ex->getMessage();
        }
        $this->getResponse()->flush();
        $this->getResponse()->appendHeader('Content-Type: application/json');
        $this->getResponse()->write(StringUtilsAbstract::getJson($results, $errors));
    }
    private function _getAccounts($params)
    {
    	$searchTxt = isset($params['searchTxt']) ? trim($params['searchTxt']) : '';
    	if($searchTxt === '')
    		throw new Exception('No search text provided.');
    	$pageNo = isset($params['pageNo']) ? trim($params['pageNo']) : 1;
    	$pageSize = isset($params['pageSize']) ? trim($params['pageSize']) : DaoQuery::DEFAUTL_PAGE_SIZE;
    	$where = 'name like ?';
    	$param = array('%' . $searchTxt . '%');
    	$rootIds = isset($params['rootIds']) ? $params['rootIds'] : array();
    	if(count($rootIds) > 0) {
    		$where .= ' and rootId in (' . implode(', ', array_fill(0, count($rootIds), '?')) . ')';
    		$param = array_merge($param, $rootIds);
    	}
    	$accounts = AccountEntry::getAllByCriteria($where, $param, true, $pageNo, $pageSize);
    	return array_map(create_function('$a', 'return $a->getJson();'), $accounts);
    }
}