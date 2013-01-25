<?php
/**
 * Asset Service
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 */
class AssetService extends BaseService
{
    /**
     * The asset type dao 
     * 
     * @var EntityDao
     */
    private $_typeDao;
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("Asset");
        $this->_typeDao = new EntityDao('AssetType');
    }
    /**
     * get all asset type
     * 
     * @param int   $pageNumber The page number
     * @param int   $pageSize   The page size
     * @param array $order      The order clause
     * 
     * @return Ambigous <multitype:, multitype:BaseEntityAbstract >
     */
    public function getAllAssetTypes($pageNumber = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $order = array())
    {
        return $this->_typeDao->findAll($pageNumber, $pageSize, $order);
    }
    /**
     * get asset type by id
     * 
     * @param int $id The id of the asset type
     * 
     * @return AssetType
     */
    public function getAllAssetType($id)
    {
        return $this->_typeDao->findById($id);
    }
    /**
     * Saving the asset type
     * 
     * @param AssetType $at   The asset type that we are trying to save. When new, it's just a new AssetType()
     * @param string    $type The new type of $at
     * @param string    $path The new path of $at
     * 
     * @return AssetType
     */
    public function saveAssetType(AssetType $at, $type, $path)
    {
        //if the new path doesn't exsit, then create one
        if(!is_dir($path))
        {
            mkdir($path);
            chmod($path, 0777);
        }
        //if the new path is different from the old path
        $originalPath = trim($at->getPath());
        if($originalPath !== $path)
        {
            rename($originalPath . '/*', $path);
        }
        
        $at->setType($type);
        $at->setPath($path);
        return $this->_typeDao->save($at);
    }
    /**
     * Saving the file onto asset table
     * 
     * @param int    $assetTypeId The id of asset type
     * @param string $filePath    The absolute path of te file
     * @param string $fileName    The name of the file
     * 
     * @throws ServiceException
     * @return Asset
     */
    public function registerFile($assetTypeid, $filePath, $fileName)
    {
        $assetType = $this->_typeDao->findById($assetTypeid);
        if(!$assetType instanceof AssetType)
            throw new ServiceException('Asset Type (ID=' . $assetTypeid . ') does NOT exsits!');
            
        $key = md5(Core::getUser() . $fileName . $assetType . microtime());
        if(!file_exists($filePath))
            throw new ServiceException('file (' . $filePath . ') does NOT exsits!');
        
        $now = new UDate();
        $dir = $assetType->getPath() . ($assetPath = $now->format('Y_m_d') . '/');
        if(!is_dir($dir))
        {
            mkdir($dir);
            chmod($dir, 0777);
        }
        $newFilePath = $dir . $key;
        rename($filePath,$newFilePath);
        $asset = new Asset();
        $asset->setAssetKey($key)
            ->setAssetType($assetType)
            ->setFilename($fileName)
            ->setMimeType($this->_getMimeType($fileName))
            ->setPath($assetPath);
        $this->save($asset);
        return $asset;
    }
    /**
     * removing the file from asset table
     * 
     * @param string $assetid The Id of the asset
     * 
     * @throws ServiceException
     * @return AssetService
     */
    public function removeFile($assetid)
    {
        $asset = $this->getAssetByKey($assetid);
        if($asset instanceof Asset)
            throw new ServiceException('Asset (key=' . $assetid . ') does NOT exsits!');

        $asset = $asset[0];
        unlink($asset->getFilePath());
        $this->entityDao->delete($asset);
        return $this;
    }
    /**
     * streaming/reading a file
     * 
     * @param string $assetid The Id of the asset
     * 
     * @throws ServiceException
     * @return AssetService
     */
    public function streamFile($assetid)
    {
        $asset = $this->getAssetByKey($assetid);
        if(!$asset instanceof Asset)
            throw new ServiceException('Asset (key=' . $assetid . ') does NOT exsits!');
		header('Content-Type: ' . $asset->getMimeType());
		readfile($asset->getFilePath());
        return $this;
    }
    /**
     * getting the asset object by the provided key
     * 
     * @param string $key The key of the asset
     * 
     * @return Asset
     */
    public function getAssetByKey($key)
    {
        $asset = $this->findByCriteria('`assetKey` = ?', array($key), true, 1, 1);
        return count($asset) > 0 ? $asset[0] : null;
    }
    /**
     * Simple method for detirmining mime type of a file based on file extension
     * This isn't technically correct, but for our problem domain, this is good enough
     *
     * @param string $filename The name of the file
     *
     * @return string
     */
    private function _getMimeType($filename)
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
            $fileSuffix = mime_content_type($filename);
    
        return "unknown/" . trim($fileSuffix[0], ".");
    }
}