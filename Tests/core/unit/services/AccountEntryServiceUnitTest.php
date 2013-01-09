<?php
/**
 * Test case for Service - AccountEntryService
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class AccountEntryServiceUnitTest extends CoreServiceUnitTestAbstract
{
   /**
     * The testing service class name
     *
     * @var string
     */
    protected $_serviceName = 'AccountEntryService';
    /**
     * testing the AccountEntryService::getNextAccountNo() function
     */
    public function testGetNextAccountNo()
    {
        $parent = $this->_serviceObj->get(1);
        $nextAccNo = $this->_serviceObj->getNextAccountNo($parent);
        $this->assertEquals(strlen($parent->getAccountNumber()) + AccountEntry::ACC_NO_LENGTH, strlen($nextAccNo));
        
        $pureNo = preg_replace('/^(' . $parent->getAccountNumber() . ')/', '', $nextAccNo);
        $this->assertTrue($pureNo >= str_repeat('0', AccountEntry::ACC_NO_LENGTH));
        $this->assertTrue($pureNo <= str_repeat('9', AccountEntry::ACC_NO_LENGTH));
        
        //completely new account number
        $parent->setAccountNumber('99999');
        $nextAccNo = $this->_serviceObj->getNextAccountNo($parent);
        $this->assertEquals($parent->getAccountNumber() . str_repeat('0', AccountEntry::ACC_NO_LENGTH), $nextAccNo);
    }
    /**
     * testing the AccountEntryService::getChildrenAccounts() function
     */
    public function testGetChildrenAccounts()
    {
        $parent = $this->_serviceObj->get(1);
        $children = $this->_serviceObj->getChildrenAccounts($parent);
        $expected = Dao::countByCriteria(new DaoQuery('AccountEntry'), 'active = 1 and parentId = ?', array($parent->getId()));
        $this->assertTrue(is_array($children));
        $this->assertEquals($expected, count($children));
        if(count($children) > 0)
            $this->assertInstanceOf(AccountEntry, $children[0]);
        
        $children = $this->_serviceObj->getChildrenAccounts($parent, true);
        $this->assertEquals($expected + 1, count($children));
        
        $expected = Dao::countByCriteria(new DaoQuery('AccountEntry'), 'active = 1 and id != ? and accountNumber like ?', array($parent->getId(), $parent->getAccountNumber() . "%"));
        $children = $this->_serviceObj->getChildrenAccounts($parent, false, false);
        $this->assertEquals($expected , count($children));
        
        $children = $this->_serviceObj->getChildrenAccounts($parent, true, false);
        $this->assertEquals($expected + 1, count($children));
    }
    /**
     * testing the AccountEntryService::getAccountFromAccountNo() function
     */
    public function testGetAccountFromAccountNo()
    {
        $parent = $this->_serviceObj->get(1);
        $this->assertEquals($parent , $this->_serviceObj->getAccountFromAccountNo($parent->getAccountNumber()));
    }
    /**
     * testing the AccountEntryService::save() function
     */
    public function testSave()
    {
        $parent = $this->_serviceObj->get(1);
        $return = $this->_serviceObj->save($parent);
        $this->assertEquals($parent, $return);
    }
    /**
     * testing the AccountEntryService::findAll() and AccountEntryService::getPageStats() function
     */
    public function testFindAll()
    {
        $expected = Dao::countByCriteria(new DaoQuery('AccountEntry'), 'active = ?', array(1));
        $accounts = $this->_serviceObj->findAll();
        $this->assertEquals($expected , count($accounts));
        if(count($accounts) > 0)
            $this->assertInstanceOf(AccountEntry, $accounts[0]);
        
        $expected = array('totalPages' => 1, 'totalRows' => count($accounts), 'pageNumber' => null, 'pageSize' => count($accounts));
        $this->assertEquals($expected , $this->_serviceObj->getPageStats());
        
    }
}
?>