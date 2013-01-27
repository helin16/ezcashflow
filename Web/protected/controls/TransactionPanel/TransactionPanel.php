<?php
/**
 * The transaction panel
 * 
 * @package    Web
 * @subpackage Controls
 * @author     lhe
 *
 */
class TransactionPanel extends TTemplateControl  
{
    /**
     * The javascript that will run after saving
     * @var string
     */
    private $_postJs;
	/**
	 * AccountEntryService
	 * 
	 * @var AccountEntryService
	 */
	private $_accountService;
	/**
	 * TransactionService
	 * 
	 * @var TransactionService
	 */
	private $_transService;
	/**
	 * The asset service
	 * 
	 * @var AssetService
	 */
	private $_assetService;
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_transService = new TransactionService();
		$this->_accountService = new AccountEntryService();
		$this->_assetService = new AssetService();
	}
	/**
	 * Getter for the postJs
	 * 
	 * @return string The javascript string
	 */
	public function getPostJs()
	{
	    return $this->postJs;
	}
	/**
	 * Setter for the postJs
	 * 
	 * @param string $postJs The post javascript after saving
	 * 
	 * @return TransactionPanel
	 */
	public function setPostJs($postJs)
	{
	    $this->postJs = $postJs;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
	    $this->getPage()->getClientScript()->registerStyleSheetFile('TransPanelCss', $this->publishAsset(__CLASS__ . '.css'));
	    $this->getPage()->getClientScript()->registerScriptFile('TransPanelJs', $this->publishAsset(__CLASS__ . '.js'));
	}
	/**
	 * Event: ajax call to get all the accounts
	 *
	 * @param TCallback          $sender The event sender
	 * @param TCallbackParameter $param  The event params
	 *
	 * @throws Exception
	 */
	public function saveTrans($sender, $param)
	{
	    $results = $errors = array();
	    try
	    {
	        if(!isset($param->CallbackParameter->fromIds) || count($fromIds = $param->CallbackParameter->fromIds) === 0)
	            throw new Exception('fromIds not found!');
	        if(!isset($param->CallbackParameter->toIds) || count($toIds = $param->CallbackParameter->toIds) === 0)
	            throw new Exception('toIds not found!');
	        if(!isset($param->CallbackParameter->fromAccId) || !($fromAccount = ($this->_accountService->get($param->CallbackParameter->fromAccId))) instanceof AccountEntry)
	            throw new Exception('fromAccId not found!');
	        if(!isset($param->CallbackParameter->toAccId) || !($toAccount = ($this->_accountService->get($param->CallbackParameter->toAccId))) instanceof AccountEntry)
	            throw new Exception('toAccId not found!');
	        if(!isset($param->CallbackParameter->value) || ($value = trim($param->CallbackParameter->value)) <= 0)
	            throw new Exception('value not found!');
	        if(!isset($param->CallbackParameter->assets))
	            throw new Exception('assets not found!');
	        $assets = json_decode(json_encode($param->CallbackParameter->assets), true);
	        $comments = !isset($param->CallbackParameter->comments) ? '' : trim($param->CallbackParameter->comments);
	        if($fromAccount->getRoot()->getId() == AccountEntry::TYPE_INCOME)
    	        $transArray = $this->_transService->earnMoney($fromAccount, $toAccount, $value, $comments);
	        else
	            $transArray = array($this->_transService->transferMoney($fromAccount, $toAccount, $value, $comments));
	        
	        $results['trans'] = array();
	        foreach($transArray as $trans)
	        {
	            foreach($assets as $asset)
	            {
	                $asset = $this->_assetService->getAssetByKey($asset['assetKey']);
	                if($asset instanceof Asset)
	                    $trans = $this->_transService->addAsset($trans, $asset);
	            }
	            $transArray = $trans->getJsonArray();
	            $transArray['link'] = '/trans/' . $trans->getId();
	            $results['trans'][] = $transArray;
	        }
	        $results = array_merge($results, $this->_getAccList($fromIds, $toIds));
	    }
	    catch(Exception $e)
	    {
	        $errors[] = $e->getMessage();
	    }
	    $param->ResponseData = Core::getJson($results, $errors);
	    return $this;
	}
	/**
	 * Event: ajax call to get all the accounts
	 *
	 * @param TCallback          $sender The event sender
	 * @param TCallbackParameter $param  The event params
	 *
	 * @throws Exception
	 */
	public function getAccounts($sender, $param)
	{
	    $results = $errors = array();
	    try
	    {
	        if(!isset($param->CallbackParameter->fromIds) || count($fromIds = $param->CallbackParameter->fromIds) === 0)
	            throw new Exception('fromIds not found!');
	        if(!isset($param->CallbackParameter->toIds) || count($toIds = $param->CallbackParameter->toIds) === 0)
	            throw new Exception('toIds not found!');
	        $results = $this->_getAccList($fromIds, $toIds);
	    }
	    catch(Exception $e)
	    {
	        $errors[] = $e->getMessage();
	    }
	    $param->ResponseData = Core::getJson($results, $errors);
	    return $this;
	}
	/**
	 * Getting the account list
	 * 
	 * @param array $fromIds The rootId for the from account
	 * @param array $toIds   The rootId fro the to account
	 * 
	 * @return Ambigous <multitype:multitype: , multitype:>
	 */
	private function _getAccList($fromIds, $toIds)
	{
	    $results = array();
	    $results['from'] = array();
	    foreach($this->_accountService->findByCriteria('id in (' . implode(', ', $fromIds) . ')') as $root)
	        $results['from'][$root->getName()] = $this->_getAccountList($root->getId());
	     
	    $results['to'] = array();
	    foreach($this->_accountService->findByCriteria('id in (' . implode(', ', $toIds) . ')') as $root)
	        $results['to'][$root->getName()] = $this->_getAccountList($root->getId());
	    return $results;
	}
	/**
	 * Getting the acocunt list
	 * 
	 * @param int $rootId The root Id
	 * 
	 * @return array
	 */
	private function _getAccountList($rootId)
	{
	    $accounts = array();
	    $results = $this->_accountService->getAllAllowTransAcc(array($rootId), null, DaoQuery::DEFAUTL_PAGE_SIZE, array('rootId' => 'asc'));
	    foreach($results as $account)
	    {
	        $accArray = $account->getJsonArray(false);
	        $accounts[$accArray['breadCrumbs']['name']] = $accArray;
	    }
	    krsort($accounts);
	    $accounts = array_reverse($accounts);
	    return $accounts;
	}
	/**
	 * File upload handler
	 * 
	 * @param TFileUploader $sender The file uploader
	 * @param Mixed         $param  The parameters
	 */
	public function fileUploaded($sender, $param)
	{
	    $assets = json_decode($this->resultJson->Value, true);
	    $key = md5(Core::getUser() . '' . new UDate());
        $fileInfo = array();
	    if($sender->HasFile)
	    {
	        try
	        {
	            $asset = $this->_assetService->registerFile(AssetType::ID_DOC, $sender->LocalName, $sender->FileName);
    	        $fileInfo['assetKey'] = $asset->getAssetKey();
    	        $fileInfo['fileName'] = $asset->getFilename();
    	        $fileInfo['filePath'] = $asset->getAssetType()->getPath() . '/' . $asset->getPath() . '/' . $asset->getAssetKey();
	        } 
	        catch(Exception $ex)
	        {
	            $fileInfo['error'] = $ex->getMessage();
	        }
	    }
	    else 
	    {
	        $fileInfo['error'] = 'There is an error occurred with code: ' . $sender->ErrorCode;
	    }
	    $assets[$key] = $fileInfo;
	    $this->resultJson->Value = json_encode($assets);
	    $this->Result->Text = $this->_displayFileList($assets);
	}
	/**
	 * getting the html code for the list of files
	 * 
	 * @param array $assets The file list
	 * 
	 * @return string
	 */
	private function _displayFileList($assets)
	{
	    $html = '<div class="fileList">';
	    $i = 0;
	    foreach($assets as $key => $fileInfo)
	    {
	        $html .= '<div class="row ' . ($i % 2 === 0 ? 'even' : 'old') . '">';
	        if(isset($fileInfo['error']) && $fileInfo['error'] !== '')
	        {
	            $html .= '<span class="error">';
	                $html .= $fileInfo['error'];
	            $html .= '</span>';
	        }
	        else
	        {
	            $html .= '<span class="file inline">';
        	        $html .= '<a href="/asset/' . $fileInfo['assetKey'] . '" target="_blank">' . $fileInfo['fileName'] . '</a>';
    	        $html .= '</span>';
    	        $html .= '<span class="btns inline">';
        	        $html .= '<a href="javascript: void(0);" onclick="transJs.removeFile(this, ' . "'" . $this->resultJson->getClientId() . "'" . ')" assetkey="' . $key . '">x</a>';
    	        $html .= '</span>';
	        }
	        $html .= '</div>';
	        $i++;
	    }
	    $html .= '</div>';
	    return $html;
	}
	/**
	* Delete the file
	*
	* @param TCallback          $sender The event sender
	* @param TCallbackParameter $param  The event params
	*
	* @throws Exception
	*/
	public function delFile($sender, $param)
	{
	    $results = $errors = array();
	    try
	    {
	        if(!isset($param->CallbackParameter->assetKey) || ($assetKey = trim($param->CallbackParameter->assetKey)) === '')
	            throw new Exception('assetKey NOT found!');
	        $this->_assetService->removeFile($assetKey);
	    }
	    catch(Exception $e)
	    {
	        $errors[] = $e->getMessage();
	    }
	    $param->ResponseData = Core::getJson($results, $errors);
	    return $this;
	}
}

?>