<?php
/**
 * This is the loginpage
 * 
 * @author lhe
 */
class LoginController extends TPage  
{
    /**
     * (non-PHPdoc)
     * @see TControl::onLoad()
     */
	public function onLoad($param)
	{
	    $this->getPage()->getClientScript()->registerStyleSheetFile('loginCss', $this->publishAsset(__CLASS__ . '.css'));
		$this->username->focus();
	}
	/**
     * Validates whether the username and password are correct.
     * This method responds to the TCustomValidator's OnServerValidate event.
     * 
     * @param mixed $sender    The event sender
     * @param mixed $parameter The event parameters
     */
    public function validateUser($sender,$param)
    {
    	$authManager=$this->Application->getModule('auth');
    	try
    	{
			if($authManager->login($this->username->Text, $this->password->Text))
				$this->Response->redirect('/');
    	}
    	catch(AuthenticationException $ex)
    	{
    	}
    }
}
?>