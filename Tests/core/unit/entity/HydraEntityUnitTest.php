<?php
/**
 * Test case for Entity - HydraEntity
 *
 * @package    Core
 * @subpackage Test
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class HydraEntityUnitTest extends CoreEntityUnitTestAbstract
{
    /**
     * As HydraEntity is acting like abstract class, we are using UserAccount to test it for now
     *
     * @var string
     */
    protected $_entityName = 'UserAccount';
    /**
     * testing the __toString function
     */
    public function testToString()
    {
       //TODO: need to test __toString()
    }
    /**
     * testing the getDate objects when eta is a string or hydradate object
     *
     * @param string $field The fields to set and get
     *
     * @dataProvider getHydraDateFields
     */
    public function testGetDateObjects($field)
    {
        $this->_testGetDateString('set' . $field, 'get' . $field);
    }
    /**
     * data provider for testGetETA
     *
     * @return multitype:string
     */
    public function getHydraDateFields()
    {
        return array(array('Created'),
            array('Updated')
        );
    }
    /**
     * test isActive()
     */
    public function testIsActive()
    {
        $this->_entityObj->setActive(true);
        $this->assertTrue($this->_entityObj->isActive(), "HydraEntity::isActive() should return true!");
        $this->_entityObj->setActive(false);
        $this->assertFalse($this->_entityObj->isActive(), "HydraEntity::isActive() should return false!");
    }
    /**
     * test loadOneToMany() with NO lazy loading
     */
    public function testLoadOneToManyWithoutLazyLoading()
    {
        //TODO: need to find a way to test this!
//         $this->_entityObj->setTokens(array(new Token()));
//         $lazyLoading = Dao::$LazyLoadingEnabled;
//         Dao::$LazyLoadingEnabled = false;
//         $this->assertEquals(null, $this->_entityObj->getTokens(), "HydraEntity::loadOneToMany() should return null!");
//         Dao::$LazyLoadingEnabled = $lazyLoading;
    }
    /**
     * test loadManyToOne() with NO lazy loading
     */
    public function testLoadManyToOneWithoutLazyLoading()
    {
        //TODO: need to find a way to test this!
//         $this->_entityObj->setWorkType(array(new WorkType()));
//         $lazyLoading = Dao::$LazyLoadingEnabled;
//         Dao::$LazyLoadingEnabled = false;
//         $this->assertEquals(null, $this->_entityObj->getWorkType(), "HydraEntity::loadManyToOne() should return null!");
//         Dao::$LazyLoadingEnabled = $lazyLoading;
    }
    /**
     * test loadManyToMany() with NO lazy loading
     */
    public function testLoadManyToManyWithoutLazyLoading()
    {
        //TODO: need to find a way to test this!
    }
    /**
     * test preSave()
     */
    public function testPreSave()
    {
        $entity = new HydraEntity();
        $this->assertEquals(null, $entity->preSave(), "HydraEntity::preSave() should return null!");
    }
    /**
     * test preSave()
     */
    public function testPostSave()
    {
        $entity = new HydraEntity();
        $this->assertEquals(null, $entity->postSave(1, true), "HydraEntity::postSave() should return null!");
    }
    /**
     * test __get()
     */
    public function testBlockingDirectGetters()
    {
        try
        {
            $entity = new HydraEntity();
            $entity->__get('id');
            $this->assertFalse(true, 'We are expecting an exception here!');
        }
        catch(Exception $ex)
        {
            $this->assertEquals('Attempted to get variable HydraEntity::id directly and it is either inaccessable or doesnt exist', $ex->getMessage());
        }
    }
    /**
     * test __set()
     */
    public function testBlockingDirectSetters()
    {
        try
        {
            $entity = new HydraEntity();
            $entity->__set('id', 1);
            $this->assertFalse(true, 'We are expecting an exception here!');
        }
        catch(Exception $ex)
        {
            $this->assertEquals('Attempted to set variable HydraEntity::id directly and it is either inaccessable or doesnt exist', $ex->getMessage());
        }
    }
    /**
     * test __loadDaoMap()
     */
    public function testBlockingDirectLoadDaoMap()
    {
        try
        {
            $entity = new HydraEntity();
            $entity->__loadDaoMap();
            $this->assertFalse(true, 'We are expecting an exception here!');
        }
        catch(Exception $ex)
        {
            $this->assertEquals('HydraEntity::__loadDaoMap() is unimplemented', $ex->getMessage());
        }
    }
    /**
     * test getString()
     */
    public function testGetString()
    {
        $entity = new HydraEntity();
        $entity->setId(1);
        $expected = 'HydraEntity (#1)';
        $this->assertEquals($expected, $entity->getString(), "HydraEntity::getString() should get '$expected'.");
    }
    /**
     * test collectLogs()
     */
    public function testCollectLogs()
    {
        $entity = new HydraEntity();
        $entity->setId(1);
//         $entity->addLog('id', 1, 2, 'comments', '2012-09-10 00:00:00', 'id', Core::getUser(), Core::getRole());
//         $this->assertEquals(array(1, 'HydraEntity', 'id', 1, 2, 'comments', '2012-09-10 00:00:00', 'id', Core::getUser(), Core::getRole()), $entity->collectLogs());
        $this->assertEquals(array(), $entity->collectLogs());
    }
    /**
     * test addValidation(), getValidations() and validate(), validateAll()
     */
    public function testValidations()
    {
//         $entity = new HydraEntity();
//         $entity->addValidation('id', new HydraRequiredFieldValidator('Id is required'));
//         $this->assertEquals(array(new HydraRequiredFieldValidator('Id is required')), $entity->getValidations('id'));
        
//         $this->assertEquals(array('Id is required'), $entity->validate('id'));
//         $this->assertEquals(array('Id is required'), $entity->validateAll());
        
//         $entity->setId(1);
//         $this->assertEquals(true, $entity->validate('id'));
//         $this->assertEquals(true, $entity->validateAll());
    }
    /**
     * test setProxyMode($bool)
     */
    public function testSetProxyMode()
    {
        $entity = new HydraEntity();
        $entity->setProxyMode(true);
        $this->assertEquals(true, $entity->getProxyMode());
        $entity->setProxyMode(false);
        $this->assertEquals(false, $entity->getProxyMode());
    }
}

?>