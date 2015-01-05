<?php
/**
 * class AdminLayout
 *
 * @package    Web
 * @subpackage Layout
 * @author     lhe
 */
class AdminLayout extends TTemplateControl
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
	    $this->getPage()->getClientScript()->registerStyleSheetFile('defaultLayoutCss', $this->publishAsset('../default/DefaultLayout.css'));
	}
}
?>