<?php
class fileUploader extends TTemplateControl
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		if(!$this->getPage()->IsPostBack || !$this->getPage()->IsCallback)
		{
			$cScripts = FrontEndPageAbstract::getLastestJS(__CLASS__);
			if (isset($cScripts['js']) && ($lastestJs = trim($cScripts['js'])) !== '')
				$this->getPage()->getClientScript()->registerScriptFile('fileUploaderJS', $this->publishAsset($cScripts['js']));
			return $this;
		}
	}
}