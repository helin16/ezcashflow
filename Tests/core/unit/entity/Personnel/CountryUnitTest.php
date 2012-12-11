<?php
/**
 * Test case for Entity - Country
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class CountryUnitTest extends CoreEntityUnitTestAbstract
{
   /**
     * The testing entity class name
     *
     * @var string
     */
    protected $_entityName = 'Country';
    /**
     * testing the __toString function
     */
    public function testToString()
    {
        $expected = 'Country name';
        $this->_entityObj->setName($expected);
        $this->_testToString($expected);
        $this->_entityObj->setName(null);
        $this->_testToString();
    }
}
?>