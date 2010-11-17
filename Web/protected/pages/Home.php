<?php
class Home extends EshopPage 
{
	public function onLoad($param)
	{
	}
	
	public function reload()
	{
		$this->Expense->reload();
		$this->Income->reload();
		$this->Transfer->reload();
	}
}
?>