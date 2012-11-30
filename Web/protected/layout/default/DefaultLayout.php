<?php
/**
 * class DefaultLayout
 * 
 * @package    Web
 * @subpackage Layout
 * @author     lhe
 */
class DefaultLayout extends TTemplateControl
{
    /**
     * (non-PHPdoc)
     * @see TControl::onLoad()
     */
	public function onLoad($param)
	{
	    $this->getPage()->getClientScript()->registerStyleSheetFile('defaultLayoutCss', $this->publishAsset(__CLASS__ . '.css'));
		if(Core::getUser() instanceof UserAccount)
		{
			$this->bottomMenu->Visible=true;
			$this->topMenu->Visible=true;
			$this->user->Text ="Welcome, ". Core::getUser()->getPerson()->getFullName()." !";
		}
		else
		{
			$this->bottomMenu->Visible=false;
			$this->topMenu->Visible=false;
			$this->user->Text ="";
		}
	}
}
?>