<?php
/**
 * Transaction service
 * 
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class TransactionService extends BaseService
{
    /**
     * constructor
     */
	public function __construct()
	{
		parent::__construct("Transaction");
	}
	/**
	 * Transfer Money
	 * 
	 * @param AccountEntry $fromAccount The from account of the transaction
	 * @param AccountEntry $toAccount   The to account of the transaction
	 * @param string       $value       The value of the transaction
	 * @param string       $comments    The comments of the transaction
	 * 
	 * @throws Exception
	 * @return Transaction The new transaction
	 * 
	 */
	public function transferMoney(AccountEntry $fromAccount, AccountEntry $toAccount, $value, $comments = "")
	{
		return $this->_createTrans($toAccount, $value, $fromAccount, $comments);
	}
	/**
	 * earn Money
	 * 
	 * @param AccountEntry $fromAccount The from account of the transaction
	 * @param AccountEntry $toAccount   The to account of the transaction
	 * @param string       $value       The value of the transaction
	 * @param string       $comments    The comments of the transaction
	 * 
	 * @throws Exception
	 * @return Transaction The new transaction
	 */
	public function earnMoney(AccountEntry $fromAccount, AccountEntry $toAccount, $value, $comments="")
	{
		$trans = $this->_createTrans($fromAccount, $value, null, $comments);
		$trans1 = $this->_createTrans($toAccount, $value, null, $comments);
		return array($trans, $trans1);
	}
	/**
	 * Internal function for create a transaction
	 * 
	 * @param AccountEntry $fromAccount The from account of the transaction
	 * @param AccountEntry $toAccount   The to account of the transaction
	 * @param string       $value       The value of the transaction
	 * @param string       $comments    The comments of the transaction
	 * 
	 * @throws Exception
	 * @return Transaction The new transaction
	 */
	private function _createTrans(AccountEntry $toAccount, $value, AccountEntry $fromAccount = null, $comments = "")
	{
	    $comments = htmlentities($comments);
	    $value = trim($value);
	    if(!is_numeric($value))
	        throw new ServiceException("Invalid value to spend!");
	    
	    if($fromAccount instanceof AccountEntry && $fromAccount->getId() === $toAccount->getId())
	        throw new ServiceException("Can't make transaction between the same account!");
	    
	    $transation = new Transaction();
	    $transation->setFrom($fromAccount);
	    $transation->setTo($toAccount);
	    $transation->setValue($value);
	    $transation->setComments($comments);
	    return $this->save($transation);
	}
    /**
     * get the sum of the expense between dates
     * 
     * @param string $startDate       The start date
     * @param string $endDate         The end date
     * @param Int    $accountTypeId   The account root Id
     * @param string $excludePosition The excluding account entries' position
     * 
     * @return double
     */
	public function getSumOfExpenseBetweenDates($startDate, $endDate, $accountTypeId = AccountEntry::TYPE_EXPENSE, $excludePosition = '')
	{
		$qry = 'select sum(t.value) as sum 
				from transaction t 
				inner join accountentry acc on (acc.active = 1 and acc.id = t.toId {innerJoin})
				where t.active = 1
				and (acc.rootId = :accTypeId) 
				and (t.created between :startDate and :endDate)';
		$params = array('accTypeId' => $accountTypeId, 'startDate' => $startDate, 'endDate' => $endDate);
		if(($excludePosition = trim($excludePosition)) !== '')
		{
			$qry = str_replace('{innerJoin}', ' and acc.accountNumber not like :excludePos', $qry);
			$params['excludePos'] = $excludePosition . '%';
		}
		else
		    $qry = str_replace('{innerJoin}', '', $qry);
		$results = Dao::getSingleResultNative($qry, $params);
		return round($results["sum"], 2);
	}
	/**
	 * Getting the top expenses
	 * 
	 * @param Int    $rootId      The root id of the expense account entries
	 * @param Int    $noOfItems   How many items
	 * @param string $startDate   The start date
	 * @param string $endDate     The end date
	 * @param string $excludingIds The excluding account entries' id
     * 
     * @return double
	 */
	public function getTopExpenses($rootId = 4, $noOfItems = 4, $startDate = '1790-01-01 00:00:00', $endDate = '9999-12-31 23:59:59', $excludingIds = array())
	{
		$qry = 'select sum(t.value) `sum`, acc.name, acc.id
				from transaction t
				inner join accountentry acc on (acc.active = 1 and acc.id = t.toId {innerJoin})
				where t.active = 1
				and (acc.rootId = :accTypeId) 
				and (t.created between :startDate and :endDate)
				group by acc.id
				order by sum desc
				limit ' . $noOfItems;
		$params = array('accTypeId' => $rootId, 'startDate' => $startDate, 'endDate' => $endDate);
		if(count($excludingIds) !== 0)
		{
			$qry = str_replace('{innerJoin}', ' and acc.id not in (:excludePos)', $qry);
			$params['excludePos'] = implode(",",$excludingIds);
		}
		else
		    $qry = str_replace('{innerJoin}', '', $qry);
		    
		$results = Dao::getSingleResultNative($qry, $params);
		return $results;
	}
	/**
	 * adding an asset to a transaction
	 * 
	 * @param Transaction $trans The transaction
	 * @param Asset       $asset The asset
	 * 
	 * @return Transaction
	 */
    public function addAsset(Transaction $trans, Asset $asset)
    {
        $this->entityDao->saveManyToManyJoin($asset, $trans);
        return $this->get($trans->getId());
    }
    /**
     * removing an asset to a transaction
     * 
     * @param Transaction $trans The transaction
	 * @param Asset       $asset The asset
	 * 
	 * @return Transaction
     */
    public function removeAsset(Transaction $trans, Asset $asset)
    {
        $this->entityDao->deleteManyToManyJoin($asset, $trans);
        return $this->get($trans->getId());
    }
    /**
     * Update all transactions
     * 
     * @param AccountEntry $oldAccount The old account
     * @param AccountEntry $newAccount The new account
     * 
     * @return TransactionService
     */
    public function moveAllTrans(AccountEntry $oldAccount, AccountEntry $newAccount)
    {
        if(!$newAccount->getAllowTrans())
            throw new ServiceException('The new account(' . $newAccount->getId() . ') is NOT allow to create any transactions!');
        //move all from accountentry
        $this->entityDao->updateByCriteria('`fromId` = ?', '`fromId` = ?', array($newAccount->getId(), $oldAccount->getId()));
        //move all to accounentry
        $this->entityDao->updateByCriteria('`toId` = ?', '`toId` = ?', array($newAccount->getId(), $oldAccount->getId()));
        return $this;
    }
}
?>