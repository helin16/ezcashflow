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
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->postJs = '';
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onInit()
	 */
	public function onInit($param)
	{
	    parent::onInit($param);
	    $this->getPage()->getClientScript()->registerStyleSheetFile('TransPanelCss', $this->publishAsset(__CLASS__ . '.css'));
	    $this->getPage()->getClientScript()->registerScriptFile('TransPanelJs', $this->publishAsset(__CLASS__ . '.js'));
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
	        Dao::beginTransaction();
	        if(!isset($param->CallbackParameter->fromIds) || count($fromIds = $param->CallbackParameter->fromIds) === 0)
	            throw new Exception('fromIds not found!');
	        if(!isset($param->CallbackParameter->toIds) || count($toIds = $param->CallbackParameter->toIds) === 0)
	            throw new Exception('toIds not found!');
	        if(!isset($param->CallbackParameter->fromAccId) || !($fromAccount = (BaseService::getInstance('AccountEntryService')->get($param->CallbackParameter->fromAccId))) instanceof AccountEntry)
	            throw new Exception('fromAccId not found!');
	        if(!isset($param->CallbackParameter->toAccId) || !($toAccount = (BaseService::getInstance('AccountEntryService')->get($param->CallbackParameter->toAccId))) instanceof AccountEntry)
	            throw new Exception('toAccId not found!');
	        if(!isset($param->CallbackParameter->value) || ($value = trim($param->CallbackParameter->value)) <= 0)
	            throw new Exception('value not found!');
	        if(!isset($param->CallbackParameter->assets))
	            throw new Exception('assets not found!');
	        $assets = json_decode(json_encode($param->CallbackParameter->assets), true);
	        $comments = !isset($param->CallbackParameter->comments) ? '' : trim($param->CallbackParameter->comments);
	        if($fromAccount->getRoot()->getId() == AccountEntry::TYPE_INCOME)
    	        $transArray = BaseService::getInstance('TransactionService')->earnMoney($fromAccount, $toAccount, $value, $comments);
	        else
	            $transArray = array(BaseService::getInstance('TransactionService')->transferMoney($fromAccount, $toAccount, $value, $comments));
	        
	        $results['trans'] = array();
	        foreach($transArray as $trans)
	        {
	            foreach($assets as $key => $asset)
	            {
	                $filePath = trim($asset['tmpDir']) . DIRECTORY_SEPARATOR . trim($asset['filepath']);
	                if(is_file($filePath))
	                    $trans = BaseService::getInstance('TransactionService')->addAsset($trans, BaseService::getInstance('AssetService')->registerFile(AssetType::ID_DOC, $filePath, trim($asset['name'])));
	            }
	            $transArray = $trans->getJsonArray();
	            $transArray['link'] = '/trans/' . $trans->getId();
	            $results['trans'][] = $transArray;
	        }
	        $results = array_merge($results, $this->_getAccList($fromIds, $toIds));
	        Dao::commitTransaction();
	    }
	    catch(Exception $e)
	    {
	        Dao::rollbackTransaction();
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
	    foreach(BaseService::getInstance('AccountEntryService')->findByCriteria('id in (' . implode(', ', $fromIds) . ')') as $root)
	        $results['from'][$root->getName()] = $this->_getAccountList($root->getId());
	     
	    $results['to'] = array();
	    foreach(BaseService::getInstance('AccountEntryService')->findByCriteria('id in (' . implode(', ', $toIds) . ')') as $root)
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
	    $results = BaseService::getInstance('AccountEntryService')->getAllAllowTransAcc(array($rootId), null, DaoQuery::DEFAUTL_PAGE_SIZE, array('rootId' => 'asc'));
	    foreach($results as $account)
	    {
	        $accArray = $account->getJsonArray(false);
	        $accounts[$accArray['breadCrumbs']['name']] = $accArray;
	    }
	    krsort($accounts);
	    $accounts = array_reverse($accounts);
	    return $accounts;
	}
}

?>