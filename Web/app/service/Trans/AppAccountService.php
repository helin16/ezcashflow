<?php
/**
 * Account service for app
 * @author lhe
 */
class AppAccountService extends AppService
{
	/**
	 * @var AccountEntryService
	 */
	private $_accountService;
	/**
	 * constructor
	 */
	public function __construct()
	{
		$this->_accountService = new AccountEntryService();
	}
	/**
	 * getting the account list
	 * 
	 * @param mixed $param The provided data from usr
	 * 
	 * @throws Exception
	 * @return Ambigous <multitype:multitype: , multitype:NULL >
	 */
	public function getAccounts($param)
	{
		$rootIds = isset($param['rootIds']) ? $param['rootIds'] : array();
		$leafOnly = (isset($param['leafOnly']) && strtolower(trim($param['leafOnly'])) === 'true') ?  true : false;
		if(count($rootIds) === 0)
			throw new Exception("Empty acount type provided");
		return $this->_getAccounts($rootIds, $leafOnly);
	}
	/**
	 * deleting the account list
	 * 
	 * @param mixed $param The provided data from usr
	 * 
	 * @throws Exception
	 * @return Ambigous <multitype:multitype: , multitype:NULL >
	 */
	public function deleteAccounts($param)
	{
		$accountIds = isset($param['accountIds']) ? $param['accountIds'] : array();
		$accounts = array();
		foreach($accountIds as $id)
		{
			if(!($account = $this->_accountService->get($id)) instanceof AccountEntry)
				continue;
			if(count($account->getChildren()) > 0)
				throw new Exception("You can't delete an account with children!");
			
			$accounts[] = $account;
		}
		
		$return = array();
		foreach($accounts as $account)
		{
			$id =$account->getId();
			$account->setActive(false);
			$this->_accountService->save($account);
			$return[$id] = self::formatAccountEntry($account);
		}
		return $return;
	}
	/**
	 * saving a account 
	 * 
	 * @param mixed $param The provided data from usr
	 * 
	 * @throws Exception
	 * @return Ambigous <multitype:multitype: , multitype:NULL >
	 */
	public function saveAccount($param)
	{
		if(!isset($param['accountInfo']))
			throw new Exception("System Error: missing accountInfo!");
		
		if(!$account = $this->_accountService->get($param['accountInfo']['accountId']) instanceof AccountEntry)
			$account = new AccountEntry();
		$account->setComments(trim($param['accountInfo']['comments']));
		$account->setValue(trim($param['accountInfo']['value']));
		$account->setBudget(trim($param['accountInfo']['budget']));
		if(($accountName = trim($param['accountInfo']['accountName'])) === '')
			throw new Exception("System Error: missing accountName!");
		$account->setName($accountName);
		
		if(($accountNumber = trim($param['accountInfo']['accountNumber'])) === '')
			throw new Exception("System Error: missing accountNumber!");
		$account->setAccountNumber($accountNumber);
		
		$parent = null;
		if(!isset($param['accountInfo']['parentId']) || ($parentId = trim($param['accountInfo']['parentId'])) === '')
		{
			if(trim($account->getId()) === '')
				throw new Exception("System Error: missing parent account!");
			$parent = $account->getParent();
		}
		else
		{
			if(trim($account->getId()) !== '')
				$parent = $account->getParent();
			else 
				$parent = $this->_accountService->get($parentId);
		}
		if (!$parent instanceof AccountEntry)
			throw new Exception("System Error: Invalid parent!");
		$account->setParent($parent);
		$account->setRoot($parent->getRoot());
		
		$this->_accountService->save($account);
		$id =$account->getId();
		$return  = array();
		$return[$id]  = self::formatAccountEntry($account);
		return $return;
	}
	/**
	 * Getting the accounts list with provided rootIds
	 * 
	 * @param int[] $rootIds     The root ids
	 * @param bool  $getLeafOnly Getting the leaf account entries only
	 * 
	 * @throws Exception
	 * @return Ambigous <multitype:multitype: , multitype:number, multitype:number string name accountNumber NULL >
	 */
	private function _getAccounts($rootIds, $getLeafOnly = false)
	{
		if(count($rootIds) === 0)
			throw new Exception("Invalid rootIds!");
		
		$service = new BaseService("AccountEntry");
		$result = $service->findByCriteria("rootId in (" . implode(",", $rootIds). ")", array(), null, 30, array("AccountEntry.rootId" => "asc", "AccountEntry.accountNumber" => "asc"));
		$array = array();
		foreach($result as $a)
		{
			if($getLeafOnly === true && count($a->getChildren()) !== 0)
				continue;
			
			$info = self::formatAccountEntry($a);
			$id = $a->getId();
			$rootId = $a->getRoot()->getId();
			if(!isset($array[$rootId]))
				$array[$rootId] = array();
			$array[$rootId][$id] = $info;
		}
		return $array;
	}
	/**
	 * Returing the app formated acocunt entry
	 * 
	 * @param AccountEntry $entry The account entry we are trying to format
	 * 
	 * @return multitype:number string name accountNumber NULL
	 */
	public static function formatAccountEntry(AccountEntry $entry)
	{
		$info = array(
			'id' => ($id = $entry->getId()),
			'name' => $entry->getName(),
			'rootId' => ($rootId = $entry->getRoot()->getId()),
			'parentId' => (($parent = $entry->getParent()) instanceof AccountEntry ? $parent->getId() : null),
			'accountNumber' => $entry->getAccountNumber(),
			'breadCrumbs' => $entry->getBreadCrumbs(),
			'value' => $entry->getValue(),
			'bugdget' => $entry->getBudget(),
			'amount' => $entry->getSum(),
			'comments' => $entry->getComments(),
			'active' => $entry->getActive(),
			'isLeaf' => (count($entry->getChildren()) === 0)
		);
		return $info;
	}
}