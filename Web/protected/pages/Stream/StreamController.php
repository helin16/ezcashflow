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
//         $response = $this->getResponse();
//         $response->clear();
//         $response->setCharset('UTF-8');
        ob_start();
  	    try
  	    {
  	        if(isset($this->Request['method']) && trim($this->Request['method']) === 'upload')
  	        {
//   	        	$response->setCharset('text/javascript');
  	            $reporting = error_reporting();
  	            error_reporting(E_ALL | E_STRICT);
  	            $upload_handler = new UploadHandler();
  	            error_reporting($reporting);
  	        }
  	        else 
        		echo BaseService::getInstance('AssetService')->streamFile($this->Request['id']);
  	    }
  	    catch(Exception $ex)
  	    {
  	        echo $ex->getMessage();
  	    }
        $contents = ob_get_contents();
        ob_end_clean();
        echo $contents;
        die;
//         $response->write($contents);
//         $response->flushContent();
  	}	
}

?>