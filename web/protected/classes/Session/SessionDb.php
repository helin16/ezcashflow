<?php
/**
 * The customised session connector
 * 
 * @package    Web
 * @subpackage Class
 * @author     lhe<helin16@gmail.com>
 */
class SessionDb extends THttpSession
{
    /**
     * The session service
     * 
     * @var SessionService
     */
    private $_sessService;
    /**
     * constructor
     */
    public function __construct()
    {
        $this->_sessService = new SessionService();
    }
    /**
    * Session open handler.
    * This method should be overridden if {@link setUseCustomStorage UseCustomStorage} is set true.
    * 
    * @param string session save path
    * @param string session name
    * 
    * @return boolean whether session is opened successfully
    */
    public function _open($savePath,$sessionName)
    {
        return true;
    }
    /**
     * Session close handler.
     * This method should be overridden if {@link setUseCustomStorage UseCustomStorage} is set true.
     * @return boolean whether session is closed successfully
     */
    public function _close()
    {
        return true;
    }
    /**
     * Session read handler.
     * This method should be overridden if {@link setUseCustomStorage UseCustomStorage} is set true.
     * @param string session ID
     * @return string the session data
     */
    public function _read($id)
    {
        return $this->_sessService->read($id);
    }
    /**
     * Session write handler.
     * This method should be overridden if {@link setUseCustomStorage UseCustomStorage} is set true.
     * @param string session ID
     * @param string session data
     * @return boolean whether session write is successful
     */
    public function _write($id,$data)
    {
        $session = $this->_sessService->write($id, $data);
        return ($session instanceof Session);
    }
    
    /**
     * Session destroy handler.
     * This method should be overridden if {@link setUseCustomStorage UseCustomStorage} is set true.
     * @param string session ID
     * @return boolean whether session is destroyed successfully
     */
    public function _destroy($id)
    {
        $this->_sessService->delete($id);
        return true;
    }
    
    /**
     * Session GC (garbage collection) handler.
     * This method should be overridden if {@link setUseCustomStorage UseCustomStorage} is set true.
     * @param integer the number of seconds after which data will be seen as 'garbage' and cleaned up.
     * @return boolean whether session is GCed successfully
     */
    public function _gc($maxLifetime)
    {
        $this->_sessService->cleanUp($maxLifetime);
        return true;
    }
}