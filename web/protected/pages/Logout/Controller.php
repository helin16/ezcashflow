<?php
class Controller extends TPage
{
	public function onLoad($param)
	{
		$redirectUrl = (isset($_REQUEST['url']) && trim($_REQUEST['url']) !== '') ? trim($_REQUEST['url']) : '/';
		Core::setUser(UserAccount::get(UserAccount::ID_GUEST_ACCOUNT), null, null);
		$auth = $this->getApplication()->Modules['auth'];
		$auth->logout();
		$this->Response->Redirect($redirectUrl);
	}
}