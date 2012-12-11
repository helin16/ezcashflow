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
        //TODO: testing NOT covered for cases 1> State=== null && postcode!= null. 2> The whole toString's length === 0
        $this->_entityObj = new Address();
        $this->_entityObj->setState(new State());
        $this->_entityObj->setCountry(new Country());
        
        $this->_entityObj->setLine1('line1');
        $this->_entityObj->setSuburb('suburb');
        $this->_testToString('line1, suburb');
        
        $this->_entityObj->setLine2('line2');
        $this->_testToString('line1, line2, suburb');
        
        $state = new State();
        $state->setName('State');
        $this->_entityObj->setState($state);
        $this->_testToString('line1, line2, suburb, State');
        
        $country = new Country();
        $country->setName('Country');
        $this->_entityObj->setCountry($country);
        $this->_testToString('line1, line2, suburb, State, Country');
        
        $this->_entityObj->setPostcode('postcode');
        $this->_testToString('line1, line2, suburb, State, Country postcode');
        
        //         $this->_entityObj->setLine1('');
        //         $this->_entityObj->setLine2('');
        //         $this->_entityObj->setSuburb('');
        //         $this->_entityObj->setPostcode('');
        //         $state = new State();
        //         $state->setName('');
        //         $this->_entityObj->setState($state);
        //         $country = new Country();
        //         $country->setName('');
        //         $this->_entityObj->setCountry($country);
        //         $this->_testToString();
    }
}
?>