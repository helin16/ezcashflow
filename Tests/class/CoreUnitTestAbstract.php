<?php
/**
 *
 * Abstract Core Unit Test
 *
 * @package    Core
 * @subpackage Test
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
abstract class CoreUnitTestAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * Whether we running the tests in debug mode
     * 
     * @var bool
     */
    protected $_debugMode = true;
    /**
     * The total number of time that we are trying to loop when we are find a random entity
     * 
     * @var Int
     */
    const TOTAL_NO_RANDOM = 1000;
    /**
     * constructor
     *
     * @param string $name     The name of the test case
     * @param array  $data     The data for testing
     * @param Mixed  $dataName The data name
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        Core::setUser(Dao::findById(new DaoQuery("UserAccount"), 1));
    }
    /**
     * pre-test for each test function 
     */
    public function setUp() 
    {
    }
    /**
     * post test for each test function
     */
    public function tearDown()
    {
        if ($this->_debugMode === true) 
        {
            $content = ob_get_contents();
            ob_flush();
            echo $this->_getDebugHeader($this->getName());
            echo $content;
            echo $this->_getDebugFooter();
        }
    }
    /**
     * Getting the header div for debug div
     * 
     * @param string $funcName The name of test function
     * 
     * @return string The HTML code
     */
    private function _getDebugHeader($funcName) 
    {
        $html = '<div class="funDebugWrapper">';
        $html .= '<div class="funDebugTitle">Debugging: ' . $funcName . '</div>';
        $html .= '<div class="funDebugContent">';
        return $html;
    }
    /**
     * get the function debug information footer
     * 
     * @return string The HTML code
     */
    private function _getDebugFooter() 
    {
        $html = '</div>';
        $html .= '</div>';
        return $html;
    }
    
    /**
     * Pass in empty site and worktype by reference
     * 
     * @param WorkType $workType The worktype object ref
     * @param Site     $site     The Site object ref    
     * 
     * @return CoreEntityUnitTestAbstract
     */
    protected function getRandomSiteAndWorkType(WorkType &$workType, Site &$site)
    {
    	for($i = 0; $i < self::TOTAL_NO_RANDOM; $i++)
    	{
    		$workType = $this->_getRandomEntity("WorkType");
    		if(!$workType instanceof WorkType || ($count = count($sites = $workType->getSites())) === 0)
    		{
    		    continue;
    		}
    		$site = $sites[rand(0, $count)];
    		if($site instanceof Site)
    		{
    		    return;
    		}
    	}
    	throw new Exception("No valid WorkType/Site object found!");
    }
    /**
     * Get Random Warehouse that allows parts
     *
     * @param Array $excludingIds
     * @return Warehouse
     */
    protected function getRandomWarehouse()
    {    	
    	return $this->_getRandomEntity("Warehouse");
    }
    /**
     * Getting a Random Part Instance object
     * 
     * @param Int $aliasType : Providing this will ensure that the part retrieved includes an alias of that type
     * 
     * @return PartInstance
     */
    protected function getRandomPartInstanceByAliasType($aliasType = PartInstanceAliasType::ID_SERIAL_NO)
    {    	
    	$count = $this->_getCountForTable('partinstance');
    	for($i = 0; $i < self::TOTAL_NO_RANDOM; $i++)
    	{
    		$pi = Dao::findById(new DaoQuery("PartInstance"), rand(1, $count));
    		if($pi instanceof PartInstance && $pi->getActive() == 1 && $pi->getAlias($aliasType) !== '')
    		{
    		    return $pi;
    		}
    	}
        throw new Exception("No valid PartInstance found for testing: " . __FUNCTION__);
    }
    /**
     * Alias function call to getRandomPartInstanceByAliasType
     *  
     * @param Int $aliasType : Providing this will ensure that the part retrieved includes an alias of that type
     * 
     * @return Ambigous <PartInstance, IHydraEntity, HydraEntity, NULL, unknown, string>
     */    
    protected function getRandomPartInstanceByAlias($aliasType = PartInstanceAliasType::ID_CLIENT_ASSET_NUMBER)
    {
        return $this->getRandomPartInstanceByAliasType($aliasType);
    }
    /**
     * Randomly getting an entity object
     *
     * @param string $entityName       The class name of the entity you are trying to get
     * @param int    $noOfLoopingTimes The total number of times that this function is trying to get the object for
     *
     * @throws Exception
     * @return Ambigous <unknown, HydraEntity, NULL, string>
     */
    protected function _getRandomEntity($entityName, $noOfLoopingTimes = self::TOTAL_NO_RANDOM)
    {
        $count = $this->_getCountForTable(strtolower($entityName));
        for($i = 0; $i < $noOfLoopingTimes; $i++)
        { 
            $entity = Dao::findById(new DaoQuery($entityName), rand(1, $count));
            if($entity instanceof $entityName)
            {
                return $entity;
            }
        }
        throw new Exception("No valid $entityName object found!");
    }
    /**
     * getting the count of an entity table
     *
     * @param string $tableName The table name that we are counting on
     *
     * @throws Exception
     */
    private function _getCountForTable($tableName)
    {
        $sql = "select count(id) from $tableName where active = 1";
        $count = Dao::getResultsNative($sql);
        if(count($count) === 0)
        {
            throw new Exception("None records found for $tableName!");
        }
        return $count[0][0];
    }
}
