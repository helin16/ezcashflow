<?php
/**
 * Test case for Dao - Config
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class ConfigUnitTest extends CoreUnitTestAbstract
{
    /**
     * testing the Config::get() function
     * 
     * @expectedExceptionMessage Service(NoService)/Name(NoClassName) not defined in config.
     */
    public function testGet()
    {
        try{
            Config::get('NoService', 'NoClassName');
        } catch(Exception $ex) {
            $this->assertEquals("Service(NoService)/Name(NoClassName) not defined in config.", $ex->getMessage());
        }
    }
}
?>