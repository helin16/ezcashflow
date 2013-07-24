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
		$accounts = array();
		foreach(BaseService::getInstance('AccountEntryService')->findAll() as $account)
		{
			$accounts[] = $account->getJsonArray();
		}
		return array('id' => $user->getId(), 'person' => $user->getPerson() . '', 'accounts' => $accounts);
	}
}