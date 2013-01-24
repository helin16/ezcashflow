<?php
/**
 * This is the home page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class Home extends PageAbstract 
{
    /**
     * (non-PHPdoc)
     * @see PageAbstract::onLoad()
     */
	public function onLoad($param) 
	{
	    parent::onLoad($param);
	    if(!$this->IsPostBack)
	    {
	        $this->_addRightPanel($this->_loadRightPanel());
	    }
	}
	/**
	 * Load right panel
	 * 
	 * @return string The HTML code in that panel
	 */
	private function _loadRightPanel()
	{
	    $html = '<div class="box">';
	        $html .= '<div class="title">Recent Trans</div>';
    	    $html .= '<div class="content" id="recentTransList">';
        	    $html .= '<img src="/contents/images/loading.gif" style="display:block; with:150px; height:150px;"/>';
    	    $html .= '</div>';
	    $html .= '</div>';
	    $html .= '<div class="box">';
    	    $html .= '<div class="title">Links</div>';
    	    $html .= '<div class="content">';
    	    $html .= $this->_getLinks();
    	    $html .= '</div>';
	    $html .= '</div>';
	    return $html;
	}
	/**
	 * Getting the links
	 * 
	 * @return string
	 */
	private function _getLinks()
	{
        $html = '<ul>';
            $html .= '<li>';
                $html .= '<a href="http://www.anz.com.au/" target="__blank">';
                    $html .= '<p class="link">Anz</p>';
                    $html .= '<p class="descr">134152326</p>';
                $html .= '</a>';
            $html .= '</li>';
            $html .= '<li>';
                $html .= '<a href="http://www.nab.com.au/" target="__blank">';
                    $html .= '<p class="link">Nab</p>';
                    $html .= '<p class="descr">13455047</p>';
                $html .= '</a>';
            $html .= '</li>';
            $html .= '<li>';
                $html .= '<a href="http://www.westpac.com.au/" target="__blank">';
                    $html .= '<p class="link">Westpac</p>';
                    $html .= '<p class="descr">92247676</p>';
                $html .= '</a>';
            $html .= '</li>';
        $html .= '</ul>';
        return $html;
	}
	/**
	 * Event: ajax call to get all the RecentTrans
	 *
	 * @param TCallback          $sender The event sender
	 * @param TCallbackParameter $param  The event params
	 *
	 * @throws Exception
	 */
	public function getRecentTrans($sender, $param)
	{
	    $results = $errors = array();
	    try
	    {
	        $service = new TransactionService();
	        $trans = $service->findByCriteria("active = ?", array(1), false, 1, 5, array("id" => "desc"));
	        foreach($trans as $tran)
	        {
	            $transArray = $tran->getJsonArray();
	            $transArray['link'] = '/trans/' . $tran->getId();
	            $results[] = $transArray;
	        }
	    }
	    catch(Exception $e)
	    {
	        $errors[] = $e->getMessage();
	    }
	    $param->ResponseData = Core::getJson($results, $errors);
	    return $this;
	}
}
?>