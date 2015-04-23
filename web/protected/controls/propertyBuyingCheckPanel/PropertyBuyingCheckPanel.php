<?php
class PropertyBuyingCheckPanel extends TTemplateControl
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		parent::onInit($param);
		if(!$this->getPage()->IsPostBack || !$this->getPage()->IsCallback)
		{
			$clientScript = $this->getPage()->getClientScript();
			$clientScript->registerPradoScript('ajax');
			$className = get_class($this);
			$scriptArray = FrontEndPageAbstract::getLastestJS($className);
			foreach($scriptArray as $key => $value) {
				if(($value = trim($value)) !== '') {
					if($key === 'js')
						$this->getPage()->getClientScript()->registerScriptFile($className . 'Js', $this->publishAsset($value));
					else if($key === 'css')
						$this->getPage()->getClientScript()->registerStyleSheetFile($className . 'Css', $this->publishAsset($value));
				}
			}
		}
	}
}