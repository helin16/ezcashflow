<?php
class EshopPage extends TPage 
{
	public $menuItemName;
	public function onPreInit($param)
	{
		parent::onPreInit($param);
		$this->getPage()->setMasterClass("Application.layout.default.DefaultLayout");
	}
	
	public function __construct()
	{
		parent::__construct();
		if(!Core::getUser() instanceof UserAccount)
		{
			$this->Response->redirect("/login.html");
		}
	}
	
	public function setInfoMsg($msg)
	{
		$this->getMaster()->InfoMsg->Text=$msg;
	}
	
	public function setErrorMsg($msg)
	{
		$this->getMaster()->ErrorMsg->Text=$msg;
	}
}
?>