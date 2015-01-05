<?php
/**
 * The Fancy Select Box
 *
 * @package    web
 * @subpackage controls
 * @author     lhe<helin16@gmail.com>
 */
class jsSaveAs extends TClientScript
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		$this->getPage()->getClientScript()->registerScriptFile('jsSaveAs', $this->publishAsset('jsSaveAs.js'));
	}
}