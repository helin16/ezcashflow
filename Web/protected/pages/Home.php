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
	
	public function reload()
	{
		$this->Expense->reload();
		$this->Income->reload();
		$this->Transfer->reload();
	}
}
?>