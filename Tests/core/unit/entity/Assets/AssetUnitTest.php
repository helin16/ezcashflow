<?php
/**
 * Test case for Entity - Asset
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class AssetUnitTest extends CoreEntityUnitTestAbstract
{
   /**
     * The testing entity class name
     *
     * @var string
     */
    protected $_entityName = 'Asset';
    /**
     * testing the __toString function
     */
    public function testToString()
    {
       $expected = 'expected File';
       $this->_entityObj->setFilename($expected);
       $this->_testToString($expected);
    }
    /**
     * testing the Asset::getFilePath() function
     */
    public function testGetFilePath()
    {
        $expected = $this->_entityObj->getAssetType()->getPath() . $this->_entityObj->getPath() . '/' . $this->_entityObj->getAssetId();
        $this->assertEquals($expected, $this->_entityObj->getFilePath());
    }
}
?>