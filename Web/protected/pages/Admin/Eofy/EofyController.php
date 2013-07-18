<?php
/**
 * This is the End Of Financial Year Report page
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class EofyController extends PageAbstract  
{
    /**
     * (non-PHPdoc)
     * @see TPage::onPreInit()
     */
    public function onPreInit($param)
    {
        $td = new TDatePicker();
        $this->getClientScript()->registerPradoScript('datepicker');
        $tdpUrl = $this->getClientScript()->getPradoScriptAssetUrl() . '/' . TDatePicker::SCRIPT_PATH . '/' . $td->getCalendarStyle() . '.css';
        $this->getClientScript()->registerStyleSheetFile($tdpUrl, $tdpUrl);
        parent::onPreInit($param);
    }
	/**
	 * Generating the excel report
	 * 
	 * @param TButton            $sender The button when clicked
	 * @param TCallbackParameter $param  The parameter when clicked fired
	 */
    public function getEOFYs($sender, $param)
    {
        $results = $errors = array();
        try
        {
            foreach(BaseService::getInstance('EOFYService')->findAll(true, null, DaoQuery::DEFAUTL_PAGE_SIZE, array('id' => "desc")) as $eofy)
            {
                $results[] = $eofy->getJsonArray();
            }
        }
        catch (Exception $ex)
        {
            $errors[] = $ex->getMessage();
        }
        $param->ResponseData = $this->_getJson($results, $errors);
        return $this;
    }
	/**
	 * saving the EOFY
	 * 
	 * @param TButton            $sender The button when clicked
	 * @param TCallbackParameter $param  The parameter when clicked fired
	 */
    public function saveEOFY($sender, $param)
    {
        $results = $errors = array();
        try
        {
            Dao::beginTransaction();
            $params = json_decode(json_encode($param->CallbackParameter), true);
            $eofy = (isset($params['id']) ? BaseService::getInstance('EOFYService')->get($params['id']) : new EOFY());
            if(!$eofy instanceof EOFY)
                throw new Exception("Invalid EOFY id provided: " . $params['id']);
            if(($start = (isset($params['start']) ? trim($params['start']) : '')) === '')
                throw new Exception('Start can NOT be blank!');
            if(($end = (isset($params['end']) ? trim($params['end']) : '')) === '')
                throw new Exception('End can NOT be blank!');
            $comments = (isset($params['comments']) ? trim($params['comments']) : '');
            $assets = (isset($params['assets']) ? $params['assets'] : array());
            $attachments = (isset($params['attachments']) ? $params['attachments'] : array());
            
            $eofy->setStart($start);
            $eofy->setEnd($end);
            $eofy->setComments($comments);
            BaseService::getInstance('EOFYService')->save($eofy);
            
            //update existing assets
    	    foreach($assets as $assetkey => $active)
    	    {
    	        if(($active !== false) || !($asset = BaseService::getInstance('AssetService')->getAssetByKey($assetkey)) instanceof Asset)
    	            continue;
        	    BaseService::getInstance('EOFYService')->removeAsset($eofy, $asset);
    	        $asset->setActive($active);
    	        BaseService::getInstance('EOFYService')->save($asset);
    	    }
    	    //creating new assets
    	    foreach($attachments as $fileKey => $asset)
    	    {
    	        $filePath = trim($asset['tmpDir']) . DIRECTORY_SEPARATOR . trim($asset['filepath']);
                if(is_file($filePath))
                    $trans = BaseService::getInstance('EOFYService')->addAsset($eofy, BaseService::getInstance('AssetService')->registerFile(AssetType::ID_DOC, $filePath, trim($asset['name'])));
    	    }
            $results = $eofy->getJsonArray();
            Dao::commitTransaction();
        }
        catch (Exception $ex)
        {
            $errors[] = $ex->getMessage();
            Dao::rollbackTransaction();
        }
        $param->ResponseData = $this->_getJson($results, $errors);
        return $this;
    }
	/**
	 * deleting the EOFY
	 * 
	 * @param TButton            $sender The button when clicked
	 * @param TCallbackParameter $param  The parameter when clicked fired
	 */
    public function deleteEOFY($sender, $param)
    {
        $results = $errors = array();
        try
        {
            Dao::beginTransaction();
            $params = json_decode(json_encode($param->CallbackParameter), true);
            $eofy = (isset($params['id']) ? BaseService::getInstance('EOFYService')->get($params['id']) : null);
            if(!$eofy instanceof EOFY)
                throw new Exception("Invalid EOFY id provided!");
            $eofy->setActive(false);
            BaseService::getInstance('EOFYService')->save($eofy);
            $results = $eofy->getJsonArray();
            Dao::commitTransaction();
        }
        catch (Exception $ex)
        {
            $errors[] = $ex->getMessage();
            Dao::rollbackTransaction();
        }
        $param->ResponseData = $this->_getJson($results, $errors);
        return $this;
    }
	/**
	 * Generating the excel report
	 * 
	 * @param TButton            $sender The button when clicked
	 * @param TCallbackParameter $param  The parameter when clicked fired
	 */
	public function genReport($sender, $param)
	{
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=ProfitsAndLost.xls");  //File name extension was wrong
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		
		$fromDate = trim($this->fromDate->Text);
		$toDate = trim($this->toDate->Text);
		echo "<table>";
			echo"<thead>";
				echo "<tr>";
					echo "<th>Acc Type</th>";
					echo "<th>Account</th>";
					echo "<th>Value</th>";
					echo "<th>Date</th>";
					echo "<th>Comments</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			foreach($this->_getTrans($fromDate, $toDate) as $path => $trans)
			{
				echo "<tr>";
					echo "<td>" . array_shift($trans['path']). "</td>";
					echo "<td>" . implode(' / ', $trans['path']). "</td>";
					echo "<td>" . $trans['value'] . "</td>";
					echo "<td>" . $trans['created'] . "</td>";
					echo "<td>" . $trans['comments'] . "</td>";
				echo "</tr>";
			}
			echo "</tbody>";
		echo "</table>";
		die;
	}
	/**
	 * Getting the transactions for the selected dates
	 * 
	 * @param string $fromDate The from date
	 * @param string $toDate   The to date
	 * 
	 * @return array
	 */
	private function _getTrans($fromDate, $toDate)
	{
		$transArray = array();
		foreach(BaseService::getInstance('TransactionService')->getTransBetweenDates($fromDate, $toDate, array(AccountEntry::TYPE_INCOME, AccountEntry::TYPE_EXPENSE)) as $trans)
		{
			$transArray[] = array('path' => explode(' / ', $trans->getTo()->getBreadCrumbs()), 'created' => $trans->getCreated(), 'value' => $trans->getValue(), 'comments' => $trans->getComments());
		}
		ksort($transArray);
		return $transArray;
	}
}
?>