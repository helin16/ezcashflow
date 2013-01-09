<?php
/**
 * Test case for Service - TransactionService
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class TransactionServiceUnitTest extends CoreServiceUnitTestAbstract
{
   /**
     * The testing service class name
     *
     * @var string
     */
    protected $_serviceName = 'TransactionService';
    /**
     * testing the TransactionService::transferMoney() function
     */
    public function testTransferMoney()
    {
        $fromAccount = Dao::findById(new DaoQuery('AccountEntry'), 1);
        $toAccount = Dao::findById(new DaoQuery('AccountEntry'), 2);
        $value = '0';
        $comments = 'some comments';
        $trans = $this->_serviceObj->transferMoney($fromAccount, $toAccount, $value, 'some comments');
        $this->assertEquals($fromAccount, $trans->getFrom());
        $this->assertEquals($toAccount, $trans->getTo());
        $this->assertEquals($value, $trans->getValue());
        $this->assertEquals($comments, $trans->getComments());
        $this->assertEquals(true, $trans->isActive());
        
        $value = '1.';
        $trans = $this->_serviceObj->transferMoney($fromAccount, $toAccount, $value, 'some comments');
        $this->assertEquals($fromAccount, $trans->getFrom());
        $this->assertEquals($toAccount, $trans->getTo());
        $this->assertEquals($value, $trans->getValue());
        $this->assertEquals($comments, $trans->getComments());
        
        try 
        {
            $this->_serviceObj->transferMoney($fromAccount, $toAccount, 'fdsafasd', 'some comments');
        }
        catch(Exception $ex)
        {
            $this->assertInstanceOf(ServiceException, $ex);
            $this->assertEquals('Invalid value to spend!', $ex->getMessage());
        }
        
        try 
        {
            $this->_serviceObj->transferMoney($fromAccount, $fromAccount, $value, 'some comments');
        }
        catch(Exception $ex)
        {
            $this->assertInstanceOf(ServiceException, $ex);
            $this->assertEquals('Can\'t make transaction between the same account!', $ex->getMessage());
        }
    }
    /**
     * testing the TransactionService::earnMoney() function
     */
    public function testEarnMoney()
    {
        $fromAccount = Dao::findById(new DaoQuery('AccountEntry'), 1);
        $toAccount = Dao::findById(new DaoQuery('AccountEntry'), 2);
        $value = '0';
        $comments = 'some comments';
        list($fromTrans, $toTrans) = $this->_serviceObj->earnMoney($fromAccount, $toAccount, $value, 'some comments');
        $this->assertEquals(null, $fromTrans->getFrom());
        $this->assertEquals($fromAccount, $fromTrans->getTo());
        $this->assertEquals($value, $fromTrans->getValue());
        $this->assertEquals($comments, $fromTrans->getComments());
        $this->assertEquals(true, $fromTrans->getActive());
        
        $this->assertEquals(null, $toTrans->getFrom());
        $this->assertEquals($toAccount, $toTrans->getTo());
        $this->assertEquals($value, $toTrans->getValue());
        $this->assertEquals(true, $toTrans->getActive());
        $this->assertEquals($comments, $toTrans->getComments());
    }
    /**
     * testing the TransactionService::getSumOfExpenseBetweenDates() function
     */
    public function testGetSumOfExpenseBetweenDates()
    {
        $now = new UDate();
        $sum = $this->_serviceObj->getSumOfExpenseBetweenDates($now, $now, AccountEntry::TYPE_EXPENSE);
        $this->assertTrue(is_numeric($sum));
        
        $sum = $this->_serviceObj->getSumOfExpenseBetweenDates($now, $now, AccountEntry::TYPE_EXPENSE, '999999999');
        $this->assertTrue(is_numeric($sum));
    }
    /**
     * testing the TransactionService::getTopExpenses() function
     */
    public function testGetTopExpenses()
    {
        $accounts = $this->_serviceObj->getTopExpenses();
        $this->assertTrue(is_array($accounts));
        
        $accounts = $this->_serviceObj->getTopExpenses(4, 5, '1790-01-01 00:00:00', '9999-12-31 23:59:59', array(1));
        $this->assertTrue(is_array($accounts));
    }
}
?>