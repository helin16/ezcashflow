<?php
/**
 * Test case for Service - UserAccountService
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class UserAccountServiceUnitTest extends CoreServiceUnitTestAbstract
{
   /**
     * The testing service class name
     *
     * @var string
     */
    protected $_serviceName = 'UserAccountService';
    /**
     * testing the UserAccountService::getUserByUsernameAndPassword() function
     */
    public function testGetUserByUsernameAndPassword()
    {
        $userAccount = $this->_serviceObj->get(1);
        try {
            $actual = $this->_serviceObj->getUserByUsernameAndPassword($userAccount->getUserName(), $userAccount->getPassword());
            $this->assertEquals($userAccount, $actual);
        } catch(Exception $ex) {
            $this->assertEquals(new AuthenticationException("No User Found!"), $ex);
        }  
    }
}
?>