<?php
 
/**
 * class DefaultLayout
 */
class DefaultLayout extends TTemplateControl
{
	public function onLoad()
	{
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