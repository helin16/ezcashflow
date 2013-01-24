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
      	    $assetService = new AssetService();
    		echo $assetService->streamFile($this->Request['id']);
    		die;
  	    }
  	    catch(Exception $ex)
  	    {
  	        die($ex->getMessage());
  	    }
  	}	
}

?>