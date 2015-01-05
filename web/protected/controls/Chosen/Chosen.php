<?php
/**
 * The file uploader for the TClientScript{@see https://github.com/harvesthq/chosen}
 * 
 * @package    Web
 * @subpackage Controls
 * @author     lhe
 */
class Chosen extends TClientScript
{
    /**
     * (non-PHPdoc)
     * @see TControl::onInit()
     */
    public function onInit($param)
    {
        parent::onInit($param);
        $this->getPage()->getClientScript()->registerStyleSheetFile('chosenCss', $this->publishAsset('js/chosen.min.css'));
        $this->getPage()->getClientScript()->registerScriptFile('chosenJs', $this->publishAsset('js/chosen.proto.min.js'));
    }
}