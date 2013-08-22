<?php
/**
 * Account Entry Service
 * 
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 */
class AccountEntryService extends BaseService 
{
    /**
     * constructor
     */
	public function __construct()
	{
		parent::__construct('AccountEntry');
	}
	/**
	 * Getting the NextAccountNo for the new children of the provided parent accountentry
	 * 
	 * @param AccountEntry $parent The parent account entry
	 * 
	 * @return int The NextAccountNo 
	 * @throws Exception
	 */
	public function getNextAccountNo(AccountEntry $parent)
	{
		return $parent->getNextAccountNo();
	}
	/**
	 * Getting all the children account entry for a parent account
	 * 
	 * @param accountentry $parent             
	 * @param bool         $includeSelf        Wether we include the provided parent account
	 * @param bool         $directChildrenOnly Whether we are just getting all the direct children
	 * @param int          $pageNumber         The page number of the pagination
	 * @param int          $pageSize           The page size of the pagination
	 * @param array        $orderBy            The order by fields. i.e.: array("id" => 'desc');
	 * 
	 * @return array The array of AccountEntry
	 */
	public function getChildrenAccounts(AccountEntry $parent,$includeSelf = false,$directChildrenOnly = true, $pageNumber = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array())
	{
		return $parent->getChildren($includeSelf, $directChildrenOnly, $pageNumber, $pageSize, $orderBy);
	}
	/**
	 * gett the AccountEntry from provided account no
	 * 
	 * @param int $accountNo The account number 
	 * 
	 * @return AccountEntry
	 */
	public function getAccountFromAccountNo($accountNo)
	{
		$accounts = $this->findByCriteria("accountNumber = ?", array($accountNo));
		if(count($accounts) === 0)
			return null;
		return $accounts[0];
	}
	/**
	 * Moving an account to another acocunt
	 * 
	 * @param AccountEntry $parent       The move to account
	 * @param AccountEntry $childAccount The moving account
	 * 
	 * @return AccountEntry
	 */
	public function moveAccount(AccountEntry $parent, AccountEntry $childAccount)
	{
	    $transStarted = false;
	    try { Dao::beginTransaction(); } catch (Exception $ex) {$transStarted = true;}
	    try 
	    {
    	    $newAccountNumber = trim($this->getNextAccountNo($parent));
    	    $oldAccountNumber = trim($childAccount->getAccountNumber());
    	    $oldParent = $childAccount->getParent();
    	    Dao::$debug = true;
    	    $this->updateByCriteria('accountNumber = CONCAT(?, substring(accountNumber, ?))', 'accountNumber like ?', array($newAccountNumber, strlen($oldAccountNumber) + 1, $oldAccountNumber . '%'));
    	    Dao::$debug = false;
    	    $childAccount = $this->get($childAccount->getId());
    	    $childAccount = $this->_saveAccount($childAccount, $parent, $childAccount->getName(), $childAccount->getValue(), $childAccount->getComments(), $childAccount->getBudget());
    	    $this->save($oldParent);
    	    if($transStarted === false)
    	        Dao::commitTransaction();
    	    return $childAccount;
	    }
	    catch(Exception $ex)
	    {
    	    if($transStarted === false)
    	        Dao::rollbackTransaction();
	        throw $ex;
	    }
	}
	/**
	 * Creating an accountentry
	 * 
	 * @param AccountEntry $account  The accountentry that we are trying to save
	 * @param AccountEntry $parent   The paren AccountEntry
	 * @param string       $name     The name of the accountentry
	 * @param float        $value    The initial value fo the AccountEntry
	 * @param string       $comments The comments of the AccountEntry
	 * @param float        $budget   The budget of the AccountEntry
	 * 
	 * @return AccountEntry
	 */
	public function createAccount(AccountEntry $parent, $name, $value = '0.00', $comments = '', $budget = '0.00')
	{
	    $name = trim($name);
	    $value = trim($value);
	    $comments = trim($comments);
	    $budget = trim($budget);
	    return $this->_saveAccount(new AccountEntry(), $parent, $name, $value, $comments, $budget);
	}
	/**
	 * Updating an accountentry
	 * 
	 * @param AccountEntry $account  The accountentry that we are trying to save
	 * @param AccountEntry $parent   The paren AccountEntry
	 * @param string       $name     The name of the accountentry
	 * @param float        $value    The initial value fo the AccountEntry
	 * @param string       $comments The comments of the AccountEntry
	 * @param float        $budget   The budget of the AccountEntry
	 * 
	 * @return AccountEntry
	 */
	public function updateAccount(AccountEntry $account, AccountEntry $parent = null, $name = null, $value = null, $comments = null, $budget = null)
	{
	    $parent = ($parent instanceof AccountEntry && $parent->getId() !== $account->getParent()->getId()) ? $parent : $account->getParent();
	    $name = trim($name);
	    $name = ($name !== null) ? trim($name) : $account->getName();
	    $value = ($value !== null) ? trim($value) : $account->getValue();
	    $comments = ($comments !== null) ? trim($comments) : $account->getComments();
	    $budget = ($budget !== null) ? trim($budget) : $account->getBudget();
	    return $this->_saveAccount($account, $parent, $name, $value, $comments, $budget);
	}
	/**
	 * Getting all account entry with allow transaction turn on
	 * 
	 * @param array  $rootIds  The ID of the root accounts
	 * @param int    $page     The page number of the pagination
	 * @param int    $pagesize The page size of the pagination
	 * @param array  $orderBy  The order by fields. i.e.: array("id" => 'desc');
	 * 
	 * @return array
	 */
	public function getAllAllowTransAcc($rootIds = array(), $pageNumber = null, $pageSize = Daoquery::DEFAUTL_PAGE_SIZE, $orderBy = array())
	{
	    return $this->findByCriteria('allowTrans = ?' . (count($rootIds) > 0 ? ' AND rootId in (' . implode(', ', $rootIds).')' : ''), array(1), true, $pageNumber, $pageSize, $orderBy);
	}
	/**
	 * Executor for the saving an accountentry
	 * 
	 * @param AccountEntry $account  The accountentry that we are trying to save
	 * @param AccountEntry $parent   The paren AccountEntry
	 * @param string       $name     The name of the accountentry
	 * @param float        $value    The initial value fo the AccountEntry
	 * @param string       $comments The comments of the AccountEntry
	 * @param float        $budget   The budget of the AccountEntry
	 * 
	 * @return AccountEntry
	 */
	private function _saveAccount(AccountEntry $account, AccountEntry $parent, $name, $value, $comments, $budget)
	{
	    $trans = EntityDao::getInstance('Transaction')->findByCriteria('toId = :id or fromId = :id', array('id' => $parent->getId()), 1, 1);
	    if(count($trans) > 0)
	        throw new ServiceException('There are transactions for the parent account, please move those transactions to somewhere else first!');
	    
	    $transStarted = false;
	    try { Dao::beginTransaction();} catch (Exception $ex) { $transStarted = true;   }
	    try 
	    {
	        //if this is a new account
    	    if(trim($account->getId()) === '')
    	    {
    	        $account->setAllowTrans(true);
    	        $parent->setAllowTrans(false);
    	        $this->save($parent);
	    	    $accountNumber = $this->getNextAccountNo($parent);
    	    }
    	    else
    	    {
    	    	$accountNumber = trim($account->getAccountNumber());
    	    }
    	    $account->setName($name);
    	    $account->setParent($parent);
    	    $account->setRoot($parent->getRoot());
    	    $account->setBudget($budget);
    	    $account->setValue($value);
    	    $account->setComments(trim($comments));
    	    $account->setAccountNumber($accountNumber);
    	    $account = $this->save($account);
    	    
    	    if($transStarted === false)
    	        Dao::commitTransaction();
    	    return $account;
	    }
	    catch(Exception $ex)
	    {
	        if($transStarted === false)
	            Dao::rollbackTransaction();
	        throw $ex;
	    }
	}
}
