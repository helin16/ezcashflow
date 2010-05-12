<?php
class EshopPage extends TPage 
{
	public function onPreInit($param)
	{
		parent::onPreInit($param);
		$this->getPage()->setMasterClass("Application.layout.default.DefaultLayout");
	}
}
?>