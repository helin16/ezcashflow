<?php
/**
 * The page menu component
 * 
 * @package    Web
 * @subpackage Controls
 * @author     lhe
 *
 */
class Menu extends TTemplateControl  
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		if(!$this->Page->IsPostBack || $param == "reload")
		{
		    if(Core::getUser() instanceof UserAccount)
		        $this->user->Text ="Welcome, ". Core::getUser()->getPerson()->getFullName()." !";
		    else
		        $this->user->Text ="";
		}
	}
	/**
	 * Whether the menu item is selected
	 * 
	 * @param string $name The name of the item
	 * 
	 * @return string 
	 */
	public function changeId($name)
	{
		$selectedItemName = "home";
		if(isset($this->Page->menuItemName)&& trim($this->Page->menuItemName)!="")
			$selectedItemName=trim(strtolower(str_replace(" ","",$this->Page->menuItemName)));
		return trim(strtolower(str_replace(" ","",$name)))==$selectedItemName ? " ID='active'" : " class='menuItem'";
	}
	/**
	 * Logging out
	 * 
	 * @param TButton $sender The event sender
	 * @param Mixed   $param  The event params
	 */
	public function logout($sender,$param)
	{
		$auth = $this->Application->Modules['auth'];
		try
		{
		    $auth->logout();
		}
		catch(Exception $ex){}
	   	$this->Response->Redirect("/login.html");
	}
}

?>