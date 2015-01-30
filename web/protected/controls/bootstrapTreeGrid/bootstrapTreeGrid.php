<?php
/**
 * The bootstrapTreeGrid Loader
 *
 * @package    web
 * @subpackage controls
 * @author     lhe<helin16@gmail.com>
 */
class bootstrapTreeGrid extends TClientScript
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		$clientScript = $this->getPage()->getClientScript();
		if(!$this->getPage()->IsPostBack || !$this->getPage()->IsCallback)
		{
			$folder = $this->publishFilePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib');
			$clientScript->registerHeadScriptFile('bootstrap.treeGrid.js', $folder . '/js/jquery.treegrid.js');
			$clientScript->registerStyleSheetFile('bootstrap.treeGrid.css', $folder . '/css/jquery.treegrid.css', 'screen');
		}
	}
}