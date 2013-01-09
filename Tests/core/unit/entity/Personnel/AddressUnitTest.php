<?php
/**
 * Test case for Entity - Address
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class AddressUnitTest extends CoreEntityUnitTestAbstract
{
   /**
     * The testing entity class name
     *
     * @var string
     */
    protected $_entityName = 'Address';
    /**
     * testing the __toString function
     */
    public function testToString()
    {
        $this->_entityObj->setLine1('line1');
        $this->_entityObj->setLine2('line2');
        $this->_entityObj->setPostcode('postcode');
        $this->_entityObj->setSuburb('suburb');
        
        $state = Dao::findById(new DaoQuery('State'), 1);
        $country = $state->getCountry();
        $this->_entityObj->setState($state);
        $this->_testToString('line1, line2, suburb, ' . $state->getName() . ', ' . $country->getName() . ' postcode');
    }
}
?>