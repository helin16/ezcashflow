<?php
/**
 * Header template
 *
 * @package    Web
 * @subpackage Layout
 * @author     lhe
 */
class Header extends TTemplateControl
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		$userLoggedIn = (Core::getUser() instanceof UserAccount && Core::getUser()->getId() !== UserAccount::ID_GUEST_ACCOUNT);
		$this->topmenu->Visible = $userLoggedIn;
	}
	/**
	 * Getting whether this menu item should be active
	 *
	 * @param string $menuItemId
	 *
	 * @return boolean
	 */
	public function isActive($menuItemId)
	{
		return trim($menuItemId) === trim($this->getPage()->getMenuItem());
	}
}
?>