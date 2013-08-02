<?php
/**
 * Assset Streamer
 *
 * @package    Web
 * @subpackage Page
 * @author     lhe<helin16@gmail.com>
 */
class JsonService extends TService
{
	/**
     * (non-PHPdoc)
     * @see TService::run()
     */
  	public function run() 
  	{		
        $response = $this->getResponse();
        $response->setCharset('UTF-8');
//         $response->setContentType('text/javascript');
        $results = $errors = array();
  	    try
  	    {
  	        $method = (isset($this->Request['method'])&& ($method = trim($this->Request['method'])) !== '') ? $method : '';
  	        if($method === '')
  	            throw new Exception('Method not provided!');
  	        $method = '_' . $method;
  	        if(!method_exists($this, $method))
  	            throw new Exception('Method not exsits: ' . $method . '!');
  	        $results = $this->$method($_REQUEST);
  	    }
  	    catch(Exception $ex)
  	    {
  	        $errors = $ex->getMessage();
  	    }
        $response->write(Core::getJson($results, $errors));
        $response->flushContent();
  	}
  	/**
  	 * Updating the account entry info for the client
  	 * 
  	 * @param array $params The request parameters
  	 * 
  	 * @return array
  	 */
  	private function _updateAccounts($params)
  	{
  	    $lastUpdatedTime = (!isset($params['lastUpdatedTime']) || trim($params['lastUpdatedTime']) === '') ? '' : new UDate(trim($params['lastUpdatedTime']));
  	    $accounts = array();
  	    if(!$lastUpdatedTime instanceof UDate || $lastUpdatedTime->getDateTime() === false)
  	        $accounts = BaseService::getInstance('AccountEntryService')->findAll();
  	    else 
  	    {
  	        $trans = BaseService::getInstance('TransactionService')->findByCriteria('updated > ?', array(trim($lastUpdatedTime)), true, null, DaoQuery::DEFAUTL_PAGE_SIZE, array('etr.accountNumber' => 'asc'));
  	        foreach($trans as $tran)
  	        {
  	            $accounts[] =  $tran->getFrom();
  	            $accounts[] =  $tran->getTo();
  	        }
  	        $accounts = array_merge($accounts, BaseService::getInstance('AccountEntryService')->findByCriteria('updated > ?', array(trim($lastUpdatedTime)), true, null, DaoQuery::DEFAUTL_PAGE_SIZE, array('etr.accountNumber' => 'asc')));
  	    }
  	    $accounts = array_filter(array_unique($accounts));
  	    $results = array();
  	    foreach($accounts as $account)
  	    {
  	        $results[$account->getRoot()->getId()][$account->getId()] = $account->getJsonArray();
  	    }
  	    return $results;
  	}
}