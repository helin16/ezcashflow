<?php
/**
 * Test case for Entity - AccountEntry
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class AccountEntryUnitTest extends CoreEntityUnitTestAbstract
{
   /**
     * The testing entity class name
     *
     * @var string
     */
    protected $_entityName = 'AccountEntry';
    /**
     * testing the __toString function
     */
    public function testToString()
    {
        $expected = $this->_entityObj->getName();
        $actual = $this->_entityObj->__toString();
        $this->assertEquals($expected, $actual, "We should get '$expected', but got '$actual'");
    }
    /**
     * testing the AccountEntry::getJsonArray() function
     */
    public function testGetJsonArray()
    {
        $acc = array();
	    $this->_entityObjNo = $this->_entityObj->getAccountNumber();
	    $acc['level'] = ceil((strlen($this->_entityObjNo) - 1) / 4);
	    $acc['id'] = $this->_entityObj->getId();
	    $acc['name'] = $this->_entityObj->getName();
	    $acc['breadCrumbs'] = array(
	        'id' => $this->_entityObj->getBreadCrumbs(true, true),
	    	'name' => $this->_entityObj->getBreadCrumbs(true, false)
	    );
	    $acc['accountNumber'] = $this->_entityObjNo;
	    $acc['value'] = $this->_entityObj->getValue();
	    $acc['budget'] = $this->_entityObj->getBudget();
	    $acc['comments'] = $this->_entityObj->getComments();
	    $acc['sum'] = $this->_entityObj->getSum(true, true);
	    $acc['gotChildren'] = count($this->_entityObj->getChildren()) !== 0;
	    $parent = $this->_entityObj->getParent();
	    $acc['parent'] = ($parent instanceof AccountEntry ? $parent->getJsonArray() : array());
        $this->assertEquals($acc, $this->_entityObj->getJsonArray());
    }
    /**
     * testing AccountEntry::getSum()
     */
    public function testGetSum()
    {
        $sum = $this->_entityObj->getSum(true, true);
        $this->assertTrue(is_numeric($sum), "We should get a number here(=$sum)!");
    }
    /**
     * testing AccountEntry::getChildren()
     */
    public function testGetChildren()
    {
        $children = $this->_entityObj->getChildren(true);
        $this->assertTrue(is_array($children), "We get an array of accounts!");
        $this->assertTrue(count($children) > 0, "We get at least one account!");
        $this->assertInstanceof('AccountEntry', $children[0], "We should get an array of AccountEntry!");
    }
    /**
     * testing AccountEntry::getParents() , AccountEntry::getParents(), AccountEntry::getLongshot(), AccountEntry::getSnapshot(), 
     */
    public function testGetParents()
    {
        $parents = $this->_entityObj->getParents(true);
        $this->assertTrue(is_array($parents), "We get an array of accounts!");
        $this->assertTrue(count($parents) > 0, "We get at least one account!");
        $this->assertInstanceof('AccountEntry', $parents[0], "We should get an array of AccountEntry!");
        
        $parents = array_reverse($parents);
        $expected = implode(' / ', array_map(create_function('$a', 'return $a->getName();'), $parents));
        $actual = $this->_entityObj->getBreadCrumbs(true, false, ' / ');
        $this->assertEquals($expected, $actual, "We should get '$expected', but got '$actual'");
        
        $expected = implode(' / ', array_map(create_function('$a', 'return $a->getId();'), $parents));
        $actual = $this->_entityObj->getBreadCrumbs(true, true, ' / ');
        $this->assertEquals($expected, $actual, "We should get '$expected', but got '$actual'");
        
        $expected = $this->_entityObj->getBreadCrumbs() . " - $" . $this->_entityObj->getSum();
        $actual = $this->_entityObj->getLongshot();
        $this->assertEquals($expected, $actual, "We should get '$expected', but got '$actual'");
        
        $expected =  $this->_entityObj->getRoot() . " - " . $this->_entityObj->getName() . " - $" . $this->_entityObj->getSum();
        $actual = $this->_entityObj->getSnapshot();
        $this->assertEquals($expected, $actual, "We should get '$expected', but got '$actual'");
    }
}
?>