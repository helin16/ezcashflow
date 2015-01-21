<?php
class fileUploader extends TClientScript
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		if(!$this->getPage()->IsPostBack || !$this->getPage()->IsCallback)
		{
			$clientScript = $this->getPage()->getClientScript();
			$folder = $this->publishFilePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'jQuery-File-Upload' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR);
			$clientScript->registerHeadScriptFile('jquery.fileupload.widget', $folder . '/vendor/jquery.ui.widget.js');
			$clientScript->registerHeadScriptFile('jquery.fileupload.iframe', $folder . '/jquery.iframe-transport.js');
			$clientScript->registerHeadScriptFile('jquery.fileupload', $folder . '/jquery.fileupload.js');
			return $this;
		}
	}
}