<?php
/**
 * Assset Streamer
 * 
 * @package    Web
 * @subpackage Page
 * @author     lhe<helin16@gmail.com>
 */
class StreamController extends TService 
{
    /**
     * (non-PHPdoc)
     * @see TService::run()
     */
  	public function run() 
  	{		
  	    try
  	    {
  	        if(isset($this->Request['method']) && trim($this->Request['method']) === 'upload')
  	        {
  	            $reporting = error_reporting();
  	            error_reporting(E_ALL | E_STRICT);
  	            $upload_handler = new UploadHandler();
  	            error_reporting($reporting);
  	        }
  	        else 
        		echo BaseService::getInstance('AssetService')->streamFile($this->Request['id']);
    		die;
  	    }
  	    catch(Exception $ex)
  	    {
  	        die($ex->getMessage());
  	    }
  	}	
}

?>