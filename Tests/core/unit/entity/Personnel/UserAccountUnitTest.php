<?php
/**
 * Test case for Entity - UserAccount
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class UserAccountUnitTest extends CoreEntityUnitTestAbstract
{
   /**
     * The testing entity class name
     *
     * @var string
     */
    protected $_entityName = 'UserAccount';
    /**
     * testing the __toString function
     */
    public function testToString()
    {
        $expected = 'my passord';
        $this->_entityObj->setUserName($expected);
        $this->_testToString($expected);
    }
}
?>