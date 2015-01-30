<?php
/**
 * The DetailsPage Page Abstract
 *
 * @package    Web
 * @subpackage Class
 * @author     lhe<helin16@gmail.com>
 */
abstract class DetailsPageAbstract extends BackEndPageAbstract
{
	/**
	 * The focusing entity's Class
	 *
	 * @var string
	 */
	protected $_focusClass = null;
	/**
	 * @var TCallback
	 */
	private $_saveItemBtn;
	/**
	 * loading the page js class files
	 */
	protected function _loadPageJsClass()
	{
		parent::_loadPageJsClass();
		$thisClass = __CLASS__;
		$cScripts = self::getLastestJS(__CLASS__);
		if (isset($cScripts['js']) && ($lastestJs = trim($cScripts['js'])) !== '')
			$this->getPage()->getClientScript()->registerScriptFile($thisClass . 'Js', $this->publishFilePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . $lastestJs));
		if (isset($cScripts['css']) && ($lastestCss = trim($cScripts['css'])) !== '')
			$this->getPage()->getClientScript()->registerStyleSheetFile($thisClass . 'Css', $this->publishFilePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . $lastestCss));
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onInit()
	 */
	public function onInit($param)
	{
		parent::onInit($param);

		$this->_saveItemBtn = new TCallback();
		$this->_saveItemBtn->ID = 'saveItemBtn';
		$this->_saveItemBtn->OnCallback = 'Page.saveItem';
		$this->getControls()->add($this->_saveItemBtn);
	}
	/**
	 * (non-PHPdoc)
	 * @see TPage::onPreInit()
	 */
	public function onPreInit($param)
	{
		parent::onPreInit($param);
		if(isset($_REQUEST['blanklayout']) && intval($_REQUEST['blanklayout']) === 1)
			$this->getPage()->setMasterClass("Application.layout.BlankLayout");
	}
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= "pageJs.setHTMLID('result-div', 'details-page-wrapper')";
		if(!($entity = $this->_getEntity()) instanceof BaseEntityAbstract)
			$js .= ".errWhenFirstLoad();";
		else {
			$js .= ".setCallbackId('saveItem', '" . $this->_saveItemBtn->getUniqueID() . "')";
			$js .= ".setEntity(" . json_encode($entity->getJson()) . ");";
		}
		return $js;
	}
	/**
	 * Getting the entity
	 *
	 * @return BaseEntityAbstract|NULL
	 */
	protected function _getEntity() {}
	/**
	 * getting the focus entity
	 *
	 * @return string
	 */
	public function getFocusEntity()
	{
		return trim($this->_focusEntity);
	}
	/**
	 * save the items
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 * @throws Exception
	 *
	 */
	public function saveItem($sender, $param){}
}
?>