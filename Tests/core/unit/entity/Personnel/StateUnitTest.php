<?php
/**
 * Test case for Entity - State
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class StateUnitTest extends CoreEntityUnitTestAbstract
{
   /**
     * The testing entity class name
     *
     * @var string
     */
    protected $_entityName = 'State';
    /**
     * testing the __toString function
     */
    public function testToString()
    {
        $this->_entityObj = new State();
        $expected = 'state name';
        $this->_entityObj->setName($expected);
        $this->_testToString($expected);
        
        $country = new Country();
        $country->setName('country name');
        $this->_entityObj->setCountry($country);
        $expected .= ' (' . $country->getName() . ') ';
        $this->_testToString($expected);
    }
}
?>