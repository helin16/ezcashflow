<?php
class LoginController extends TPage  
{
	public function onLoad($param)
	{
		$this->username->focus();
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