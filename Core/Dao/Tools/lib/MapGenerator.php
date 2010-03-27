<?php

class MapGenerator extends FileScanner
{
	public function __construct($path)
	{
		parent::__construct($path);		
	}
	
	public function generateMapFile($filename)
	{	
		file_put_contents($filename,"<?php\n return unserialize('".serialize(array(Map::$fieldMap,Map::$databaseMap))."');\n?>");
	}
}

?>