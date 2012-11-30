<?php
/**
 * Transaction service for app
 * @author lhe
 */
class AppTransService extends AppService
{
	/**
	 * @var AccountEntryService
	 */
	private $_accountService;
	/**
	 * @var TransactionService
	 */
	private $_transService;
	/**
	 * constructor
	 */
	public function __construct()
	{
		$this->_accountService = new AccountEntryService();
		$this->_transService = new TransactionService();
	}
	/**
	 * the public function to record the transactions
	 * 
	 * @param mixed $param The input data
	 * 
	 * @throws Exception
	 * @return Mixed
	 */
	public function recordTrans($param)
	{
		if(!isset($param['trans']))
			throw new Exception("System Error: Invalid information for Trans!");
		if(!isset($param['trans']['action']))
			throw new Exception("System Error: Invalid action type for Trans!");
		$action = trim($param['trans']['action']);
		
		$fromAccountId = ((isset($param['trans']['fromAccountId']) && (trim($param['trans']['fromAccountId']) !== '' )) ? trim($param['trans']['fromAccountId']) : '');
		if(!($fromAccount = $this->_accountService->get($fromAccountId)) instanceof AccountEntry)
			throw new Exception("Invalid from account!");
		$toAccountId = ((isset($param['trans']['toAccountId']) && (trim($param['trans']['toAccountId']) !== '' )) ? trim($param['trans']['toAccountId']) : '');
		if(!($toAccount = $this->_accountService->get($toAccountId)) instanceof AccountEntry)
			throw new Exception("Invalid to account!");
		
		$amount = ((isset($param['trans']['amount']) && (trim($param['trans']['amount']) !== '')) ? trim($param['trans']['amount']) : '');
		$comments = ((isset($param['trans']['comments']) && trim($param['trans']['comments']) !== '') ? trim($param['trans']['comments']) : '');
		$transId = ((isset($param['trans']['id']) && (($id = trim($param['trans']['id'])) !== '')) ? $id : '');
		if(($transaction = $this->_transService->get($transId)) instanceof Transaction)
		{
			$transaction->setFrom($fromAccount);
			$transaction->setTo($toAccount);
			$transaction->setValue($amount);
			$transaction->setComments($comments);
			$this->_transService->save($transaction);
		}
		else if($action === 'Income')
			$this->_transService->earnMoney($fromAccount, $toAccount, $amount, $comments);
		else
			$this->_transService->transferMoney($fromAccount, $toAccount, $amount, $comments);
		
		return self::returnAccountsAfter($fromAccount, $toAccount);
	}
	/**
	 * returning the related accounts info for refreshing clientside database
	 * 
	 * @param AccountEntry $fromAccount The from account
	 * @param AccountEntry $toAccount   The to account
	 * 
	 * @return Ambigous <multitype:, multitype:number, multitype:boolean number string name accountNumber value NULL >
	 */
	public static function returnAccountsAfter(AccountEntry $fromAccount, AccountEntry $toAccount)
	{
		$array = array();
		foreach(array_merge($fromAccount->getParents(true), $toAccount->getParents(true)) as $account) {
			$id = $account->getId();
			$array[$id] = AppAccountService::formatAccountEntry($account);
		}
		return $array;
	}
	/**
	 * the public function to delete the transactions
	 * 
	 * @param mixed $param The input data
	 * 
	 * @throws Exception
	 * @return Mixed
	 */
	public function deleteTrans($param)
	{
		if(!isset($param['transIds']))
			throw new Exception("System Error: Invalid information for TransIds!");
		
		$trans = array();
		foreach($param['transIds'] as $id)
		{
			if(!($t = $this->_transService->get($id)) instanceof Transaction)
				continue;
			
			$t->setActive(false);
			$this->_transService->save($t);
			$trans = array_merge(self::returnAccountsAfter($t->getFrom(), $t->getTo()));
		}
		return $trans;
	}
	/**
	 * the public function to record the transactions
	 * 
	 * @param mixed $param The input data
	 * 
	 * @throws Exception
	 * @return Mixed
	 */
	public function getTrans($param)
	{
		if(!isset($param['searchInfo']))
			throw new Exception("System Error: Invalid searchInfo for Trans!");
		
		$fromDate = trim($param['searchInfo']['fromDate']);		
		$toDate = trim($param['searchInfo']['toDate']);		
		$pageNo = trim($param['searchInfo']['pageNo']);		
		$pageSize = trim($param['searchInfo']['pageSize']);	
		$records = $this->_transService->findByCriteria("created >= '$fromDate' and created <= '$toDate'", true, $pageNo, $pageSize, array("Transaction.id" => "desc"));
		$total = Dao::getTotalRows();
		$trans = array('total' => $total, 'tans' => array());
		foreach($records as $t)
		{
			$trans['tans'][] = self::formatTranscation($t);
		}
		return $trans;
	}
	/**
	 * returning the app formated Transaction
	 * 
	 * @param Transaction $trans
	 * 
	 * @return Mixed
	 */
	public static function formatTranscation(Transaction $trans)
	{
		$info = array(
			'id'       => $trans->getId(),
			'from'     => (($from = $trans->getFrom()) instanceof AccountEntry ? AppAccountService::formatAccountEntry($from) : null),
			'to'     => (($to = $trans->getTo()) instanceof AccountEntry ? AppAccountService::formatAccountEntry($to) : null),
			'value'    => $trans->getValue(),
			'comments' => $trans->getComments(),
			'created'  => $trans->getCreated() . "",
			'updated'  => $trans->getUpdated() . "",
		);
		return $info;
	}
}