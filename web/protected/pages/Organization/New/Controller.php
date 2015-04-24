<?php
class Controller extends FrontEndPageAbstract
{
    protected function _getEndJs()
    {
        $js = parent::_getEndJs();
        $js .= "pageJs";
        $js .= ".setCallbackId('validUsername', '" . $this->isUsernameValidBtn->getUniqueID() . "')";
        $js .= ".init('#" .  $this->getPage()->getForm()->getClientId() . "')";
        $js .= ";";
        return $js;
    }
	/**
     * Checking whether the username is valid
     *
     * @param unknown $param
     *
     * @return boolean
     */
    public function IsUsernameValid($sender, $param)
    {
    	try {
    		if(!isset($param->CallbackParameter->query) || ($query = trim($param->CallbackParameter->query)))
    			throw new Exception('System Error: no query is provide.');
    	} catch (Exception $ex) {

    	}
    }
}