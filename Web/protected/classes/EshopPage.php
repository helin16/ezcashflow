<?php
class EshopPage extends TPage 
{
	public function onPreInit($param)
	{
		parent::onPreInit($param);
		$this->getPage()->setMasterClass("Application.layout.default.DefaultLayout");
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