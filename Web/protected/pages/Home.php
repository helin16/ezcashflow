<?php
class Home extends EshopPage 
{
	public function onLoad($param)
	{
		if(!Core::getUser() instanceof UserAccount)
		{
			$this->Response->redirect("/login.html");
		}
	}
}
?>