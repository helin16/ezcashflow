<?php
/**
 * User account service for app
 * @author lhe
 */
class AppUserService extends AppService
{
	/**
	 * translating the auth token into useraccount object
	 * 
	 * @param unknown_type $token
	 * 
	 * @return Ambigous <multitype:>
	 */
	protected function getUser($param)
	{
		$user = Core::getUser();
		$accountService = new AccountEntryService();
		$accounts = array();
		foreach($accountService->findByCriteria("active = 1", true, null, 30, array("AccountEntry.rootId" => "asc", "AccountEntry.accountNumber" => "asc")) as $account)
		{
			$rootId = $account->getRoot()->getId();
			$accounts[$rootId][$account->getAccountNumber()] = AppAccountService::formatAccountEntry($account);
		}
		return array('id' => $user->getId(), 'person' => $user->getPerson() . '', 'accounts' => $accounts);
	}
}