<?php
/**
 * This is the home page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class Home extends EshopPage 
{
    /**
     * (non-PHPdoc)
     * @see EshopPage::onLoad()
     */
	public function onLoad($param) 
	{
	    parent::onLoad($param);
	    if(!$this->IsPostBack)
	    {
	        $this->_addRightPanel($this->_loadRightPanel());
	    }
	}
	private function _loadRightPanel()
	{
	    $html = '<div class="box">';
	        $html .= '<div class="title">Recent Trans</div>';
    	    $html .= '<div class="content">';
    	    $html .= $this->_getRecentTrans();
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
	* (non-PHPdoc)
	* @see TPanel::renderEndTag()
	*/
	private function _getRecentTrans()
	{
	    $html = '<ul>';
	    $service = new TransactionService();
	    $trans = $service->findByCriteria("active = ?", array(1), false, 1, 5, array("id" => "desc"));
	    foreach($trans as $tran)
	    {
	        $fromAcc = $tran->getFrom();
	        $toAcc = $tran->getTo();
	        $html .= '<li class="row" transid="' . $tran->getId() . '">';
	        $html .= "<a href='". $this->_makeURLToReport($fromAcc, $toAcc, $tran->getCreated()) . "'>";
	        $html .= '<p class="value">$' . $tran->getValue() . '</p>';
	        $html .= '<p class="from">From: '. (!$fromAcc instanceof AccountEntry ? '' : $fromAcc->getName()) . '</p>';
	        $html .= '<p class="to">To: '.  $toAcc->getName() . '</p>';
	        $html .= '<p class="comments">'. $tran->getComments() . '</p>';
	        $html .= '</a>';
	        $html .= '</li>';
	    }
	    $html .= '</ul>';
	    return $html;
	}
	/**
	 * getting the href for that transaction
	 *
	 * @param AccountEntry $fromAccount The from of the transaction
	 * @param AccountEntry $toAccount   The to of the transaction
	 * @param UDate        $transTime   The date time of the transaction
	 *
	 * @return string The href string
	 */
	private function _makeURLToReport(AccountEntry $fromAccount = null, AccountEntry $toAccount, UDate $transTime)
	{
	    $fromDate = $transTime->__toString();
	    $transTime->modify('+1 second');
	    $toDate = $transTime->__toString();
	    $vars = array("fromAccountIds" => (!$fromAccount instanceof AccountEntry ? array() : array($fromAccount->getId())),
	                "toAccountIds" => array($toAccount->getId()),
	                "fromDate" => $fromDate,
	                "toDate" => $toDate
	    );
	    $serial = serialize($vars);
	    return "/reports/$serial";
	}
	/**
	 * reload the page
	 */
	public function reload()
	{
		$this->Expense->reload();
		$this->Income->reload();
		$this->Transfer->reload();
	}
}
?>