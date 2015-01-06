<?php
/**
 * Footer template
 *
 * @package    Web
 * @subpackage Layout
 * @author     lhe
 */
class Footer extends TTemplateControl
{
    /**
     * (non-PHPdoc)
     * @see TControl::onLoad()
     */
	public function onLoad($param)
	{
		parent::onLoad($param);
		$userLoggedIn = (Core::getUser() instanceof UserAccount && Core::getUser()->getId() !== UserAccount::ID_GUEST_ACCOUNT);
		$this->footer->Visible = $userLoggedIn;
	}
	public function getCurrentYear()
	{
		$now = new UDate();
		return $now->format('Y');
	}
}
?>