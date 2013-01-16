<?php
/**
 * The Page Abstract
 * 
 * @package    Web
 * @subpackage Class
 * @author     lhe<helin16@gmail.com>
 */
abstract class PageAbstract extends TPage 
{
    /**
     * The selected Menu Item name
     * 
     * @var string
     */
	public $menuItemName;
	/**
	 * constructor
	 */
	public function __construct()
	{
	    parent::__construct();
	    if(!Core::getUser() instanceof UserAccount)
	        $this->Response->redirect("/login.html");
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
	    if(!$this->IsPostBack)
	    {}
	}
	/**
	 * Adding a control to the RightPanel
	 * 
	 * @param mixed $newControls The new control
	 * 
	 * @return PageAbstract
	 */
	protected function _addRightPanel($newControls)
	{
	    $template = $this->getMaster();
	    if(!$template->contentRightPane instanceof TPanel)
	        return $this;
	    
	    $template->contentRightPane->getControls()->add($newControls);
        if(method_exists($template, 'setView'))
            $template->setView();
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see TPage::onPreInit()
	 */
	public function onPreInit($param)
	{
		parent::onPreInit($param);
		$this->getPage()->setMasterClass("Application.layout.default.DefaultLayout");
	}
	/**
	 * (non-PHPdoc)
	 * @see TPage::render()
	 */
	public function onInit($param)
	{
	    parent::onInit($param);
        $this->getPage()->getClientScript()->registerScriptFile('ezCashflowJs', $this->publishAsset(__CLASS__ . '.js', __CLASS__));
	    try
	    {
	        
	        //loading controller.js
	        $className = get_class($this);
	        $class = new ReflectionClass($className);
	        $fileDir = dirname($class->getFileName()) . DIRECTORY_SEPARATOR;
	        if (is_dir($fileDir))
	        {
	            //loop through the directory to find the lastes js version or css version
	            $lastestJs = $lastestJsVersionNo = $lastestCss = $lastestCssVersionNo = '';
	            if ($handle = opendir($fileDir))
	            {
	                while (false !== ($fileName = readdir($handle)))
	                {
	                    preg_match("/^" . $className . "\.([0-9]+\.)?(js|css)$/i", $fileName, $versionNo);
	                    if (isset($versionNo[0]) && isset($versionNo[1]) && isset($versionNo[2]))
	                    {
	                        $type = trim(strtolower($versionNo[2]));
	                        $version = str_replace('.', '', trim($versionNo[1]));
	                        if ($type === 'js') //if loading a javascript
	                        {
	                            if ($lastestJs === '' || $version > $lastestJsVersionNo)
	                            $lastestJs = trim($versionNo[0]);
	                        }
	                        else if ($type === 'css')
	                        {
	                            if ($lastestCss === '' || $version > $lastestCssVersionNo)
	                            $lastestCss = trim($versionNo[0]);
	                        }
	                    }
	                }
	            }
	            if ($lastestJs !== '')
	                $this->getPage()->getClientScript()->registerScriptFile('pageJs', $this->publishAsset($lastestJs));
	            if ($lastestCss !== '')
	                $this->getPage()->getClientScript()->registerStyleSheetFile('pageCss', $this->publishAsset($lastestCss));
	        }
	    }
	    catch(Exception $e)
	    {
	        //we are not doing anything if we failed here!
	    }
	}
	/**
	 * Setting the information message
	 * 
	 * @param string $msg The new information message
	 * 
	 * @return PageAbstract
	 */
	public function setInfoMsg($msg)
	{
	    if($this->getMaster()->InfoMsg instanceof TLabel)
		    $this->getMaster()->InfoMsg->Text=$msg;
	    return $this;
	}
    /**
     * Setting the error message
     * 
	 * @param string $msg The new error message
	 * 
	 * @return PageAbstract
     */	
	public function setErrorMsg($msg)
	{
	    if($this->getMaster()->ErrorMsg instanceof TLabel)
    		$this->getMaster()->ErrorMsg->Text=$msg;
	    return $this;
	}
	/**
	 * getting the JSON string
	 * 
	 * @param array $data   The result data
	 * @param array $errors The errors
	 * 
	 * @return string The json string
	 */
	protected function _getJson($data = array(), $errors = array())
	{
	    return Core::getJson($data, $errors);
	}
}
?>