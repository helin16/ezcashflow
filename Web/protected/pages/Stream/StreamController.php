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
  	        else if((isset($this->Request['method']) && trim($this->Request['method']) === 'zipped') && isset($this->Request['ids']) && ($ids = trim($this->Request['ids'])) !== '')
  	        {
  	            $assetIds = json_decode($ids);
  	            if(count($assetIds) === 0)
  	                throw new Exception('Nothing to zip!');
                $zip = new ZipArchive();
                $file = tempnam("/tmp", "zip");
                if ($zip->open($file, ZipArchive::OVERWRITE) !== TRUE)
                    throw new Exception("cannot open temp file!");
                
                foreach(BaseService::getInstance('AssetService')->findByCriteria("`assetKey` in ('" . implode("', '", $assetIds) . "')") as $asset)
                    $zip->addFile($asset->getFilePath(), $asset->getFileName());
                $zip->close();
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="attachments.zip"');
                echo readfile($file);
                unlink($file);
  	        }
  	        else 
        		BaseService::getInstance('AssetService')->streamFile($this->Request['id']);
  	    }
  	    catch(Exception $ex)
  	    {
  	        echo $ex->getMessage();
  	    }
        die;
//         $response->write($contents);
//         $response->flushContent();
  	}	
}

?>