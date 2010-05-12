<?php

/**
 * Main util for accessing/storing content in shared storage
 *
 * @package ContentServer
 * @subpackage Util
 */
class AssetServer
{
	// NOTE: These need to match up with the entries in the contenttype table
	const TYPE_GRAPH  = 1;
	const TYPE_REPORT = 2;
	const TYPE_STATIC = 3;
	
	/**
	 * @var GenericDAO
	 * @var GenericDAO
	 */
	private $contentDAO;
	
	/**
	 * @var GenericDAO
	 */
	private $contentTypeDAO;
	
	public function __construct()
	{
		$this->contentDAO = new GenericDao('Asset');
		$this->contentTypeDAO = new GenericDao('AssetType');
	}
	
	/**
	 * Register a file with the content server and get its asset id
	 *
	 * @param int $type
	 * @param string $filename
	 * @param string $data
	 * @return string 32 char MD5 hash
	 */
	public function registerAsset($type, $filename, $data)
	{
		$contentType = $this->contentTypeDAO->findById($type);
		
		$content = new Asset();
		$content->setAssetType($contentType);
		$content->setFilename($filename);
		
		$dt = new HydraDate();
		$assetId = md5($type . '::' . $filename . '::' . $dt);
		$content->setAssetId($assetId);

		$path = $this->buildSmartPath($assetId);
		$content->setPath($path);
		
		$content->setMimeType($this->getMimeType($filename));
		preg_match("|\.([a-z0-9]{2,4})$|i", $filename, $fileSuffix);
		
		$this->contentDAO->save($content);
		
		// Write data to NAS, create directory
		mkdir($contentType->getPath() . $path, 0777, true);
		$this->replaceAsset($assetId, $data,strtolower($fileSuffix[1]));
		
		return $assetId;
	}
	
	/**
	 * Replace an existing asset with a block of data
	 *
	 * @param string $assetId
	 * @param string $data
	 */
	public function replaceAsset($assetId, $data,$type="")
	{
		$content = $this->contentDAO->findByCriteria('assetId=?', array($assetId));
		$content = $content[0];
		$contentType = $content->getAssetType();
		
		file_put_contents($contentType->getPath() . $content->getPath() . '/' . $assetId.".$type", $data);
	}

	/**
	 * Stream content directly to the browser
	 *
	 * @param string $assetId
	 */
	public function streamAsset($assetId)
	{
		$content = $this->contentDAO->findByCriteria('assetId=?', array($assetId));
		$content = $content[0];
		$contentType = $content->getAssetType();
		
		header('Content-Type: ' . $content->getMimeType());
		readfile($contentType->getPath() . $content->getPath() . '/' . $assetId);
	}
	
	public function getAsset($assetId)
	{
		$content = $this->contentDAO->findByCriteria('assetId=?', array($assetId));
		if (!empty($content))
			$content = $content[0];
		return $content;		
	}
	
	/**
	 * Get a url that links directly to the asset
	 *
	 * @param string $assetId
	 * @return string
	 */
	public function getUrl($assetId,$param)
	{
		return "/asset/$assetId/$param";
	}

	/**
	 * Remove an asset from the content server
	 *
	 * @param string $assetId
	 * @return bool
	 */
	public function removeAsset($assetId)
	{
		$content = $this->contentDAO->findByCriteria('assetId=?', array($assetId));
		$content = $content[0];
		$contentType = $content->getAssetType();
		
		// Delete the item from the database
		Dao::execSql('delete from asset where assetId=?', array($assetId));
		
		// Remove the file from the NAS server
		unlink($contentType->getPath() . $content->getPath() . '/' . $assetId);
	}
	
	/**
	 * Return the path allocated to an asset. This will create them in a "load balanced" path structure
	 *   eg. assetId=1234567 will build a path /1/2/3/4
	 *       paths are always based on the first 4 characters with the entire file in that directory
	 *
	 * @param string $assetId
	 * @return string
	 */
	private function buildSmartPath($assetId)
	{
		$chars = str_split($assetId);

		$path = '';
		
		for ($i=0; $i<=4; $i++)
		{
			$path .= '/' . $chars[$i];
		}
		
		return $path;
	}
	
	/**
	 * Simple method for detirmining mime type of a file based on file extension
	 * This isn't technically correct, but for our problem domain, this is good enough
	 *
	 * @param string $filename
	 * @return string
	 */
	private function getMimeType($filename)
	{
        preg_match("|\.([a-z0-9]{2,4})$|i", $filename, $fileSuffix);

        switch(strtolower($fileSuffix[1]))
        {
            case "js" :
                return "application/x-javascript";

            case "json" :
                return "application/json";

            case "jpg" :
            case "jpeg" :
            case "jpe" :
                return "image/jpg";

            case "png" :
            case "gif" :
            case "bmp" :
            case "tiff" :
                return "image/".strtolower($fileSuffix[1]);

            case "css" :
                return "text/css";

            case "xml" :
                return "application/xml";

            case "doc" :
            case "docx" :
                return "application/msword";

            case "xls" :
            case "xlt" :
            case "xlm" :
            case "xld" :
            case "xla" :
            case "xlc" :
            case "xlw" :
            case "xll" :
                return "application/vnd.ms-excel";

            case "ppt" :
            case "pps" :
                return "application/vnd.ms-powerpoint";

            case "rtf" :
                return "application/rtf";

            case "pdf" :
                return "application/pdf";

            case "html" :
            case "htm" :
            case "php" :
                return "text/html";

            case "txt" :
                return "text/plain";

            case "mpeg" :
            case "mpg" :
            case "mpe" :
                return "video/mpeg";

            case "mp3" :
                return "audio/mpeg3";

            case "wav" :
                return "audio/wav";

            case "aiff" :
            case "aif" :
                return "audio/aiff";

            case "avi" :
                return "video/msvideo";

            case "wmv" :
                return "video/x-ms-wmv";

            case "mov" :
                return "video/quicktime";

            case "zip" :
                return "application/zip";

            case "tar" :
                return "application/x-tar";

            case "swf" :
                return "application/x-shockwave-flash";

            default :
        }
        		
		if(function_exists("mime_content_type"))
		{
			$fileSuffix = mime_content_type($filename);
		}

		return "unknown/" . trim($fileSuffix[0], ".");
	}
}

?>