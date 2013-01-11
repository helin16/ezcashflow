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
	    $this->setView();
	}
	/**
	 * Setting the view layouts
	 * 
	 * @return DefaultLayout
	 */
	public function setView()
	{
	    $hasRight = (count($this->contentRightPane->getControls()) > 1);
	    $rightPanelWidth = ($hasRight === true) ? 25 : 0;
        $this->contentRightPane->Visible = $hasRight;
	    $this->contentMainPane->Width = (95 - $rightPanelWidth) . '%';
	    $this->contentRightPane->Width = $rightPanelWidth . '%';
	    return $this;
	}
}
?>