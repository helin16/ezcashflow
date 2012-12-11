<?php
/**
 * Test case for Entity - Person
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class PersonUnitTest extends CoreEntityUnitTestAbstract
{
   /**
     * The testing entity class name
     *
     * @var string
     */
    protected $_entityName = 'Person';
    /**
     * testing toString and getFullName function
     */
    public function testToStringAndGetFullName()
    {
        $this->_entityObj->setFirstName('firstName');
        $this->_entityObj->setLastName('lastName');
        $this->_testToString($this->_entityObj->getFullName());
        
        $this->_entityObj->setFirstName('firstName');
        $this->_entityObj->setLastName('');
        $this->_testToString($this->_entityObj->getFullName());
        
        $this->_entityObj->setFirstName('');
        $this->_entityObj->setLastName('lastName');
        $this->_testToString($this->_entityObj->getFullName());
        
        $this->_entityObj->setFirstName('');
        $this->_entityObj->setLastName('');
        $this->_testToString();
    }
}
?>