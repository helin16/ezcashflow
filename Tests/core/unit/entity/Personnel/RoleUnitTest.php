<?php
/**
 * Test case for Entity - Role
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class RoleUnitTest extends CoreEntityUnitTestAbstract
{
   /**
     * The testing entity class name
     *
     * @var string
     */
    protected $_entityName = 'Role';
    /**
     * testing the __toString function
     */
    public function testToString()
    {
        $expected = $this->_entityName . ' name';
        $this->_entityObj->setName($expected);
        $this->_testToString($expected);
        $this->_entityObj->setName(null);
        $this->_testToString();
    }
}
?>