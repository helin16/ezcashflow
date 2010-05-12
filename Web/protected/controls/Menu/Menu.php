<?php

class Menu extends TTemplateControl  
{
	
	public function onLoad($param)
	{
		if(!$this->Page->IsPostBack || $param == "reload")
		{
		}
	}
	
	public function changeId($name)
	{
		$selectedItemName = "home";
		if(isset($this->Page->menuItemName)&& trim($this->Page->menuItemName)!="")
			$selectedItemName=trim(strtolower(str_replace(" ","",$this->Page->menuItemName)));
		return trim(strtolower(str_replace(" ","",$name)))==$selectedItemName ? " ID='active'" : " class='menuItem'";
	}
	
	public function logout($sender,$param)
	{
		$auth = $this->Application->Modules['auth'];

		try{
			$auth->logout();
		}
		catch(Exception $ex){}
		
	   	$this->Response->Redirect("/");
	}
}

?>