<?php
/**
 * The Recent Transactions
 *
 * @package    Web
 * @subpackage Controls
 * @author     lhe
 *
 */
class RecentTrans extends TPanel
{
    private $_howMany = 10;
    /**
     * (non-PHPdoc)
     * @see TPanel::renderEndTag()
     */
    public function renderEndTag($writer)
    {
        $html = '<ul>';
        foreach($this->_getRecentTrans() as $tran)
        {
            $fromAcc = $tran->getFrom();
            $toAcc = $tran->getTo();
            $html .= '<li>';
                $html .= "<a href='". $this->_makeURLToReport($fromAcc, $toAcc, $tran->getCreated()) . "'>";
                    $html .= '<p class="link">$' . $tran->getValue() . '</p>';
                    $html .= '<p class="descr">'. $fromAcc->getName() . ' -> ' . $toAcc->getName() . '</p>';
                    $html .= '<p class="descr">'. $tran->getComments() . '</p>';
                $html .= '</a>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        $writer->write($html);
        parent::renderEndTag($writer);
    }
    /**
     * getting the href for that transaction
     * 
     * @param AccountEntry $fromAccount The from of the transaction
     * @param AccountEntry $toAccount   The to of the transaction
     * @param HydraDate    $transTime   The date time of the transaction
     * 
     * @return string The href string
     */
    private function _makeURLToReport(AccountEntry $fromAccount, AccountEntry $toAccount, HydraDate $transTime)
    {
        $fromDate = $transTime->__toString();
        $transTime->modify('+1 second');
        $toDate = $transTime->__toString();
        $vars = array("fromAccountIds" => array($fromAccount->getId()),
                "toAccountIds" => array($toAccount->getId()),
                "fromDate" => $fromDate,
                "toDate" => $toDate
        );
        $serial = serialize($vars);
        return "/reports/$serial";
    }
    /**
     * getting the recent Transactions
     * 
     * @return array
     */
    private function _getRecentTrans()
    {
        $service = new TransactionService();
        return $service->findByCriteria("active = 1", false, 1, $this->_howMany, array("Transaction.id" => "desc"));
    }
    /**
     * get the how many trans we are going to display
     * 
     * @return Int
     */
    public function getHowMany()
    {
        return $this->_howMany;
    }
    /**
     * Setter for $_howMany
     * 
     * @param Int $howMany The how many trans we are going to display
     * 
     * @return RecentTrans
     */
    public function setHowMany($howMany)
    {
        $this->_howMany = $howMany;
        return $this;
    }
}