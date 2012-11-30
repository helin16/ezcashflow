<?php
/**
 * class DefaultLayout
 * 
 * @package    Web
 * @subpackage Layout
 * @author     lhe
 */
class DefaultLayout extends TTemplateControl
{
    /**
     * (non-PHPdoc)
     * @see TControl::onLoad()
     */
	public function onLoad($param)
	{
	    $this->getPage()->getClientScript()->registerStyleSheetFile('defaultLayoutCss', $this->publishAsset(__CLASS__ . '.css'));
	}
}
?>