<?php
/**
 * This is the home page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class Home extends EshopPage 
{
    /**
     * (non-PHPdoc)
     * @see TControl::onLoad()
     */
	public function onLoad($param) {}
	/**
	 * reload the page
	 */
	public function reload()
	{
		$this->Expense->reload();
		$this->Income->reload();
		$this->Transfer->reload();
	}
}
?>