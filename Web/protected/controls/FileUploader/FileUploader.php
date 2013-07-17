<?php
class FileUploader extends TClientScript
{
    public function onInit($param)
    {
        parent::onInit($param);
        $this->getPage()->getClientScript()->registerStyleSheetFile('fileUploaderControlCss', $this->publishAsset(__CLASS__ . '.css'));
        $this->getPage()->getClientScript()->registerScriptFile('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
        $this->getPage()->getClientScript()->registerBeginScript('jquery_no_conflict', 'var $j = jQuery.noConflict();');
        $this->getPage()->getClientScript()->registerScriptFile('jqueryUiwidget', $this->publishAsset('js/vendor/jquery.ui.widget.js'));
        $this->getPage()->getClientScript()->registerScriptFile('jqueryIFrameTrans', $this->publishAsset('js/jquery.iframe-transport.js'));
        $this->getPage()->getClientScript()->registerScriptFile('jqueryFileUploader', $this->publishAsset('js/jquery.fileupload.js'));
        $this->getPage()->getClientScript()->registerScriptFile('fileUploaderControlJs', $this->publishAsset(__CLASS__ . '.js'));
    }
}