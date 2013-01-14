<?php
/**
 * Test case for Entity - Transaction
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class TransactionUnitTest extends CoreEntityUnitTestAbstract
{
   /**
     * The testing entity class name
     *
     * @var string
     */
    protected $_entityName = 'Transaction';
    /**
     * testing the Transaction::getJsonArray() function
     */
    public function testGetJsonArray()
    {
        $tran = array();
    	$tran['id'] = $this->_entityObj->getId();
    	$tran['value'] = $this->_entityObj->getValue();
    	$tran['comments'] = $this->_entityObj->getComments();
    	$tran['fromAcc'] = $this->_entityObj->getFrom()->getJsonArray();
    	$tran['toAcc'] = $this->_entityObj->getTo()->getJsonArray();
    	$tran['created'] = $this->_entityObj->getCreated() . '';
        $this->assertEquals($tran, $this->_entityObj->getJsonArray());
    }
}
?>