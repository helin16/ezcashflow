<?php
/**
 * This is the Asset Service
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 *
 */
class Controller extends TService
{
    /**
     * (non-PHPdoc)
     * @see TService::run()
     */
    public function run()
    {
    	$results = $errors = array();
        try
        {
            $method = '_' . ((isset($this->Request['method']) && trim($this->Request['method']) !== '') ? trim($this->Request['method']) : '');
            if(!method_exists($this, $method))
                throw new Exception('No such a method: ' . $method . '!');

            $this->$method($_REQUEST);
        }
        catch (Exception $ex)
        {
        	die($ex->getMessage());
        }
    }
    private function _get($params)
    {
    	if(!isset($params['id']) || ($assetId = trim($params['id'])) === '')
    		throw new Exception('Nothing to get!');
    	$asset = null;
    	//try to use apc
    	if(extension_loaded('apc') && ini_get('apc.enabled'))
    	{
    		if(!apc_exists($assetId))
    		{
    			$asset = Asset::getAsset($assetId);
    			apc_add($assetId, $asset);
    		}
    		else
    		{
    			$asset = apc_fetch($assetId);
    		}

    	}
    	else
    	{
    		$asset = Asset::getAsset($assetId);
    	}

    	if(!$asset instanceof Asset)
    		throw new Exception('invalid id(' . $assetId . ') to get!');
    	$this->getResponse()->writeFile($asset->getFileName(), $asset->getContent()->getContent(), $asset->getMimeType(), null, true);
    }

    private function _upload($params)
    {
    	$results = $errors = array();
    	try {
	    	error_reporting(E_ALL | E_STRICT);
	    	require_once dirname(__FILE__) . '/UploadHandler.php';
	    	$dir = '/tmp/' . md5(new UDate()) . '/';
	    	$upload_handler = new UploadHandler(array(
	    		'upload_dir' => $dir,
	    		'print_response' => false
	    	));
	    	$response = $upload_handler->get_response();
	    	if(count($response) === 0)
	    		throw new Exception('System Error: can NOT get the response back.');
	    	if(!isset($response['files'][0]) || !isset($response['files'][0]->name))
	    		throw new Exception('System Error: File failed to upload.');
	    	$filePath = $dir . ($filename = $response['files'][0]->name);
	    	if(!is_file($filePath))
	    		throw new Exception('System Error: file not exsits:' . $filePath);
	    	$results['file'] = array('path' => $filePath, 'name' => $filename);
    	} catch (Exception $ex) {
    		$errors[] = $ex->getMessage() . print_r($response, true);
    	}
    	$this->getResponse()->flush();
    	$this->getResponse()->appendHeader('Content-Type: application/json');
    	$this->getResponse()->write(StringUtilsAbstract::getJson($results, $errors));
    }

    private function _getFile($params)
    {
    	if (! isset ( $params ['fileName'] ) || ($fileName = trim ( $params ['fileName'] )) === '')
			throw new Exception ( 'No fileName to get!' );
    	if (! isset ( $params ['filePath'] ) || ($filePath = trim ( $params ['filePath'] )) === '')
			throw new Exception ( 'No filePath to get!' );

    	$this->getResponse()->writeFile($fileName, file_get_contents($filePath), StringUtilsAbstract::getMimeType($fileName), null, true);
    }
}