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
	    parent::__construct("AccountEntry");
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
		$parentAccountNumber = $parent->getAccountNumber();
		$sql="select accountNumber from accountentry where active = 1 and accountNumber like '" . $parentAccountNumber . "____' order by accountNumber asc";
		$result = Dao::getResultsNative($sql);
		if(count($result) === 0)
		    return $parent->getAccountNumber() . str_repeat('0', AccountEntry::ACC_NO_LENGTH);
		
		$expectedAccountNos = array_map(create_function('$a', 'return "' . $parentAccountNumber . '".str_pad($a, ' . AccountEntry::ACC_NO_LENGTH . ', 0, STR_PAD_LEFT);'), range(0, str_repeat('9', AccountEntry::ACC_NO_LENGTH)));
		$usedAccountNos = array_map(create_function('$a', 'return $a["accountNumber"];'), $result);
		$unUsed = array_diff($expectedAccountNos, $usedAccountNos);
		sort($unUsed);
		if (count($unUsed) === 0)
			throw new ServiceException("account number over loaded (parentId = " . $parent->getId() . ", parentAccNo = $parentAccountNumber)!");
		
		return $unUsed[0];
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
}
