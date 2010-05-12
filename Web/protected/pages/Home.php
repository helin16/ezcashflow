<?php
class Home extends EshopPage 
{
	public function onLoad($param)
	{
		if(!Core::getUser() instanceof UserAccount)
		{
			$this->loginForm->Visible=true;
			$this->welcomePanel->Visible=false;
			$this->user->Text="";
			$this->username->focus();
		}
		else
		{
			$this->loginForm->Visible=false;
			$this->welcomePanel->Visible=true;
			$this->user->Text = Core::getUser()->getPerson()->getFullName();
		}
	}
	
	/**
     * Validates whether the username and password are correct.
     * This method responds to the TCustomValidator's OnServerValidate event.
     * @param mixed event sender
     * @param mixed event parameter
     */
    public function validateUser($sender,$param)
    {
    	$this->errorMessage->Text="";
    	$authManager=$this->Application->getModule('auth');
    	try
    	{
			if($authManager->login($this->username->Text, $this->password->Text))
			{
				$this->Response->redirect('/');
			}
    	}
    	catch(AuthenticationException $ex)
    	{
	    	$this->errorMessage->Text="Invalid User!";
    	}
    }
}
?>