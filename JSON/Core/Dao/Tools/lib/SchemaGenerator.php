<?php

Dao::$Server = 'ImportNode';
class SchemaGenerator
{
	private $isVersioned = array();
	
	private function directoryToArray($directory, $recursive, array $exclude)
	{
		$exclude[] = ".";
		$exclude[] = "..";
		$array_items = array();
		if ($handle = opendir($directory)) 
		{
			while (false !== ($file = readdir($handle))) 
			{
				if(!in_array($file,$exclude)) 
				{
					if (is_dir($directory. "/" . $file) && $recursive) 
					{
						$array_items = array_merge($array_items, $this->directoryToArray($directory. "/" . $file, $recursive,$exclude));
					}
					$file = $directory . '/' . $file;
					
					if (is_file($file))
						$array_items[] = $file;
				}
			}
			closedir($handle);
		}
		return $array_items;
	}

	public function __construct($directory, array $exclude)
	{
		$files = $this->directoryToArray($directory, true, $exclude);
		//var_dump($files);
		foreach($files as $file)
		{
			$path = pathinfo($file);
			$class = new $path['filename']();
			try
			{
				$class->__loadDaoMap();
				$this->isVersioned[strtolower(get_class($class))] = ($class instanceof HydraVersionedEntity);
			} catch (Exception $e)
			{
				
			}
		}
	}
	
	private function generateDropTable($class,$data)
	{
		if (isset($data['_']['engine']) && Config::get('Database', 'NASNode') != 'localhost')
			return array();
			
		$output = array();
		$output[] = "DROP TABLE IF EXISTS `$class`;\n";
		foreach($data as $var => $mods)
		{
			if(isset($mods['rel']) && $mods['rel'] == DaoMap::MANY_TO_MANY && $mods['side'] == DaoMap::LEFT_SIDE)
			{
				$right = strtolower(substr($class,0,1)).substr($class,1);
				$left = strtolower(substr($mods['class'],0,1)).substr($mods['class'],1);
				$mm = strtolower($left)."_".strtolower($right);

				$output[] = "DROP TABLE IF EXISTS `$mm`;\n";
			}
		}

		return $output;
	}
	
	public function generateDrop()
	{
		$output = array();
		$map = DaoMap::$map;
		foreach($map as $class => $value)
		{
			$output = array_merge($output,$this->generateDropTable($class,$value));			
		}
		return $output;
	}
	
	private function generateIndex($class, $fields, $data)
	{
		for ($i=0; $i<count($fields); $i++)
		{
			if (isset($data[$fields[$i]]['rel']))
			{
				$fields[$i] .= 'Id';
			}
		}
		
		$fields = '`' . implode('`,`', $fields) . '`';
		$output = "INDEX ($fields)\n";
		return $output;
	}
	
	private function generateUniqueIndex($class, $fields, $data)
	{
		for ($i=0; $i<count($fields); $i++)
		{
			if (isset($data[$fields[$i]]['rel']))
			{
				$fields[$i] .= 'Id';
			}
		}
		
		$fields = '`' . implode('`,`', $fields) . '`';
		$output = "UNIQUE INDEX ($fields)\n";
		return $output;
	}
	
	private function generateCreateTable($class, $data)
	{
		$mms = array();
		$mm = null;
		$engine = null;
		$conf = null;

		if (isset($data['_']['engine']))
		{
			// This entry should only exist on federated tables which are stored on a different db server
			// Ensure the target table exists on the remote system
			
			list($engine, $conf) = $data['_']['engine'];
			
			$host = Config::get('Database', 'NASNode');
			$driver = Config::get('Database', 'Driver');
			$schema = Config::get('Database', 'CoreDatabase');
			$user = Config::get('Database', 'Username');
			$pass = Config::get('Database', 'Password');
			
			// Only run this if the NasNode is configured to a different server, otherwise its a dev environment
			if ($host != 'localhost')
			{
				// Load the federated table into the NAS
				$fedhost = Config::get($conf, 'Host');
				$feddriver = Config::get($conf, 'Driver');
				$fedport = Config::get($conf, 'Port');
				$fedschema = Config::get($conf, 'Database');
				$feduser = Config::get($conf, 'Username');
				$fedpass = Config::get($conf, 'Password');
				
				// DSN FORMAT: "mysql://user:pass@host/database/table"
				if (strlen($fedpass) > 0)
					$feddsn = $feddriver . '://' . $feduser . ':' . $fedpass . '@' . $fedhost . '/' . $fedschema . '/' . $class;
				else
					$feddsn = $feddriver . '://' . $feduser . '@' . $fedhost . '/' . $fedschema . '/' . $class;
					
				// DSN FORMAT: "mysql:host=localhost;dbname=test"
				$dsn = $driver . ':host=' . $host . ';dbname=' . $fedschema;
				
				$db = new PDO($dsn, $user, $pass);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				$dropSQL = "DROP TABLE IF EXISTS `$class`";
				$stmt = $db->prepare($dropSQL);
				$stmt->execute();
				
				$output = "CREATE TABLE `$class` (\n";
				$output .= "\t`id` int(10) unsigned NOT NULL AUTO_INCREMENT,\n";
				
				foreach($data as $var => $mods)
				{
					if($var == '_')
						continue;
								
					if(isset($mods['rel']))
					{
						if($mods['rel'] == DaoMap::ONE_TO_ONE && $mods['owner'])
						{
							$output .= $this->generateVariable($var.'Id', $mods);
						} else if($mods['rel'] == DaoMap::MANY_TO_ONE)
						{
							$output .= $this->generateVariable($var.'Id', $mods);
						}
					} else 
						$output .= $this->generateVariable($var, $mods);
				}
				
				$output .= "\tPRIMARY KEY (`id`)\n";

				// You can't put indexes on the federated table, only the innodb table
				$tmpOutput = $output . ") ";
				
				if (isset($data['_']['index']))
				{
					foreach ($data['_']['index'] as $fields)
					{
						$output .= "\t," . $this->generateIndex($class, $fields, $data);
					}
				}
				
				if (isset($data['_']['unique']))
				{
					foreach ($data['_']['unique'] as $fields)
					{
						$output .= "\t," . $this->generateUniqueIndex($class, $fields, $data);
					}
				}
				
				$output .= ") ";
				$output .= "ENGINE=InnoDB DEFAULT CHARSET=utf8;\n";				
				$stmt = $db->prepare($output);
				$stmt->execute();

				
				// Create the federated tables
				$output = $tmpOutput . "ENGINE=FEDERATED DEFAULT CHARSET=utf8 CONNECTION='$feddsn'";
				
				// Load the federated table into the secondary db node in the cluster (db2)
				$host = Config::get('Database', 'SecondaryNode');
				$dsn = $driver . ':host=' . $host . ';dbname=' . $schema;
				$db = new PDO($dsn, $user, $pass);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				$stmt = $db->prepare($dropSQL);
				$stmt->execute();
				
				$stmt = $db->prepare($output);
				$stmt->execute();
				
				// Load the federated table into the primary db node in the cluster (db1)
				$host = Config::get('Database', 'ImportNode');
				$dsn = $driver . ':host=' . $host . ';dbname=' . $schema;
				$db = new PDO($dsn, $user, $pass);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				$stmt = $db->prepare($dropSQL);
				$stmt->execute();
				
				$stmt = $db->prepare($output);
				$stmt->execute();
				
				return array();
			}
		}
		
		$output = "CREATE TABLE `$class` (\n";
		$output .= "\t`id` int(10) unsigned NOT NULL AUTO_INCREMENT,\n";
		
		foreach($data as $var => $mods)
		{
			if($var == '_')
				continue;
						
			if(isset($mods['rel']))
			{
				if($mods['rel'] == DaoMap::ONE_TO_ONE && $mods['owner'])
				{
					$output .= $this->generateVariable($var.'Id', $mods);
				} else if($mods['rel'] == DaoMap::MANY_TO_ONE)
				{
					$output .= $this->generateVariable($var.'Id', $mods);
				} else if($mods['rel'] == DaoMap::ONE_TO_MANY)
				{
					
				} else if($mods['rel'] == DaoMap::MANY_TO_MANY && $mods['side'] == DaoMap::LEFT_SIDE)
				{
					// CREATE MANY TO MANY
					$right = strtolower(substr($class,0,1)).substr($class,1);
					$left = strtolower(substr($mods['class'],0,1)).substr($mods['class'],1);
					$mm = "CREATE TABLE `".strtolower($left)."_".strtolower($right)."` (\n";
					$mm .= "\t`".$left."Id` int(8) unsigned NOT NULL,\n";
  					$mm .= "\t`".$right."Id` int(8) unsigned NOT NULL,\n";
  					$mm .= "\t`created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,\n";
					$mm .= "\t`createdById` int(8) unsigned NOT NULL,\n";
  					$mm .= "\tUNIQUE KEY `uniq_".$left."_".$right."` (`".$left."Id`,`".$right."Id`),\n";
  					$mm .= "\tKEY `idx_".$left."_".$right."_".$left."Id` (`".$left."Id`),\n";
  					$mm .= "\tKEY `idx_".$left."_".$right."_".$right."Id` (`".$right."Id`)\n";
					$mm .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
					$mms[] = $mm;
				}
			} else 
				$output .= $this->generateVariable($var, $mods);
		}
		
		// These are automatically added by the DaoMap via HydraEntity
//		$output .= "\t`active` int(1) NOT NULL default 1,\n";
//		$output .= "\t`created` datetime NOT NULL,\n";
//		$output .= "\t`createdBy` int(8) unsigned NOT NULL,\n";
//		$output .= "\t`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n";
//		$output .= "\t`updatedBy` int(8) unsigned NOT NULL,\n";
		
		$output .= "\tPRIMARY KEY (`id`)\n";
		
		if (isset($data['_']['index']))
		{
			foreach ($data['_']['index'] as $fields)
			{
				$output .= "\t," . $this->generateIndex($class, $fields, $data);
			}
		}
		
		if (isset($data['_']['unique']))
		{
			foreach ($data['_']['unique'] as $fields)
			{
				$output .= "\t," . $this->generateUniqueIndex($class, $fields, $data);
			}
		}
		
		$output .= ") ";
		
//		if ((strtolower($GLOBALS['engine']) == 'ndbcluster' || strtolower($GLOBALS['engine']) == 'ndb') && isset($data['_']['tablespace']))
//		{
//			$output .= 'TABLESPACE ' . $data['_']['tablespace'] . ' STORAGE DISK ';
//		}
		
		$output .= "ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
		
		return array_merge(array($output),$mms);
	}

	private function generateRawVariable($var,$mods)
	{
		return "`$var` ".$this->generateDatatype($mods).$this->generateSigned($mods).$this->generateNull($mods).$this->generateDefault($mods);
	}
	
	private function generateVariable($var,$mods)
	{
		return "\t".$this->generateRawVariable($var,$mods).",\n";
	}
	
	private function generateDatatype($mods)
	{
		switch(strtolower($mods['type']))
		{
			case 'tinytext':
			case 'text':
			case 'mediumtext':
			case 'largetext':
			case 'tinyblob':
			case 'blob':
			case 'mediumblob':
			case 'largeblob':
			case 'date':
			case 'datetime':
			case 'timestamp':
			case 'bool':
			case 'float':
				return $mods['type'].' ';
				break;
			default:
				return $mods['type'].'('.$mods['size'].') ';
				break;
		}
	}
	
	private function generateSigned($mods)
	{
		return (isset($mods['unsigned']) && $mods['unsigned'] === true) ? 'unsigned ' : '';
	}
	
	private function generateNull($mods)
	{
		return (isset($mods['nullable']) && $mods['nullable'] === true) ? 'NULL ' : 'NOT NULL ';
	}
	
	private function generateDefault($mods)
	{
		$output = "";

		if (stripos($mods['type'], 'text') !== false || stripos($mods['type'], 'blob') !== false)
			return '';
			
		// Special case
		if ($mods['type'] == 'timestamp')
			return 'DEFAULT ' . $mods['default'];
		
		if(isset($mods['default']))
		{
			$value = $mods['default'];
			$output .= "DEFAULT ";
			if(isset($mods['rel']) && $mods['nullable'])
			{
				$output .= "NULL";
			} else if(is_string($value))
			{
				$output .= "'$value'";
			} else if(is_bool($value))
			{
				$output .= ($value === true) ? "true" : "false";
			} else if(is_integer($value))
			{
				$output .= $value;
			} else {
				// BAD
				$output .= $value;
			}
		}
		
		return $output;
	}
	
	public function generateCreate()
	{
		//var_dump(DaoMap::$map);
		$output = array();
		$map = DaoMap::$map;
		foreach($map as $class => $value)
		{
			$output = array_merge($output,$this->generateCreateTable($class, $value));
		}
		return $output;		
	}
	
	public function updateClassCheck($class,$newValues,$oldValues,$doDrop=false)
	{
		$output = array();
		// Do drop alters
		foreach($oldValues as $field => $mods)
		{
			if($field == '_')
				continue;
				
			if(!isset($newValues[$field]))
			{			
				// DROP (ALTER NULLABLE)
				if(isset($mods['rel']))
				{
					if($mods['rel'] == DaoMap::ONE_TO_ONE && $mods['owner'])
					{
						if($doDrop)
							$output[] = "ALTER TABLE `$class` CHANGE `$field"."Id` NULL ;\n";
						else
							$output[] = "ALTER TABLE `$class` DROP `$field"."Id`;\n";
					} else if($mods['rel'] == DaoMap::MANY_TO_ONE)
					{
						if($doDrop)
							$output[] = "ALTER TABLE `$class` CHANGE `$field"."Id` NULL ;\n";
						else
							$output[] = "ALTER TABLE `$class` DROP `$field"."Id`;\n";  
					} else if($mods['rel'] == DaoMap::ONE_TO_MANY)
					{
						
					} else if($mods['rel'] == DaoMap::MANY_TO_MANY && $mods['side'] == DaoMap::LEFT_SIDE)
					{
						
					}
				} else 
					if($doDrop)
						$output[] = "ALTER TABLE `$class` CHANGE `$field` NULL ;\n";
					else
						$output[] = "ALTER TABLE `$class` DROP `$field`;\n";
				
			}
		}
		
		
		// Do Create Update Alters
		foreach($newValues as $field => $mods)
		{
			if($field == '_')
				continue;
							
			if(isset($oldValues[$field]))
			{
				// Update
				if($mods != $oldValues[$field])
				{
					if(isset($mods['rel']))
					{
						if($mods['rel'] == DaoMap::ONE_TO_ONE && $mods['owner'])
						{
							$output[] = "ALTER TABLE `$class` CHANGE `$field` ".$this->generateRawVariable($field.'Id',$mods).";\n";
						} else if($mods['rel'] == DaoMap::MANY_TO_ONE)
						{
							$output[] = "ALTER TABLE `$class` CHANGE `$field` ".$this->generateRawVariable($field.'Id',$mods).";\n";
						} else if($mods['rel'] == DaoMap::ONE_TO_MANY)
						{
							
						} else if($mods['rel'] == DaoMap::MANY_TO_MANY && $mods['side'] == DaoMap::LEFT_SIDE)
						{
	
						}
					} else 
						$output[] = "ALTER TABLE `$class` CHANGE `$field` ".$this->generateRawVariable($field,$mods).";\n";						
				}
			} else {
				// Create
				if(isset($mods['rel']))
				{
					if($mods['rel'] == DaoMap::ONE_TO_ONE && $mods['owner'])
					{
						$output[] = "ALTER TABLE `$class` ADD ".$this->generateRawVariable($field.'Id', $mods).";\n";
					} else if($mods['rel'] == DaoMap::MANY_TO_ONE)
					{
						$output[] = "ALTER TABLE `$class` ADD ".$this->generateRawVariable($field.'Id', $mods).";\n";
					} else if($mods['rel'] == DaoMap::ONE_TO_MANY)
					{
						
					} else if($mods['rel'] == DaoMap::MANY_TO_MANY && $mods['side'] == DaoMap::LEFT_SIDE)
					{

					}
				} else 
					$output[] = "ALTER TABLE `$class` ADD ".$this->generateRawVariable($field, $mods).";\n";	
			}
		}
		
		return $output;
	}
	
	public function generateUpdate($previousMap,$doDrops=false)
	{
		$output = array();
		$map = DaoMap::$map;
		
		
		if($doDrops)
		{
			// Do drops
			foreach($previousMap as $class => $value)
			{			
				if(!isset($map[$class]))
				{
					$output = array_merge($output,$this->generateDropTable($class,$value));
				}
			}
		}	
		
		// Do Creates and Updates
		foreach($map as $class => $value)
		{
			// Check If class Exists in old schema
			if(isset($previousMap[$class]))
			{
				// Check sub elements
				$output = array_merge($output,$this->updateClassCheck($class,$value,$previousMap[$class]));
								
			} else {
				// Create Class
				$output = array_merge($output,$this->generateCreateTable($class, $value));
			}
		}
		
		return $output;
	}

	public function saveMap($file)
	{
		$map = DaoMap::$map;
		$output = serialize($map);
		file_put_contents($file,$output);
	}
	
	public function loadMap($file)
	{
		return unserialize(file_get_contents($file));
	}
	
	public function setupDatabase($importFile=null,$echoDropCreate=false,$echoImport=false)
	{
		$drop = $this->generateDrop();
		$create = $this->generateCreate();
		
		if(sizeof($drop) != sizeof($create))
		{
			throw new Exception("Size of Created Drop and Create Command lists isnt equal (".sizeof($drop).",".sizeof($create)."), Note from Peter Beardsley: This is a bad thing you should investigate.");
		}
		
		if($echoDropCreate)
			echo "Setting Up Database\n";
		Dao::connect();
		for($i=0;$i<sizeof($drop);$i++)
		{
			if($echoDropCreate)
				echo $drop[$i];
			Dao::execSql($drop[$i]);
			if($echoDropCreate)
				echo $create[$i];
			Dao::execSql($create[$i]);
		}

		if($importFile != null)
		{

			if($echoImport)
				echo "\n\nImporting Data ($importFile)\n";

			try {
				$data = explode("\n",file_get_contents($importFile));
			} catch (Exception $e)
			{
				echo "File \"$importFile\" Failed to open.\n";
				return; 	
			}
			
			foreach($data as $row)
			{
				$sql = trim($row);
				if(strlen($sql) > 0)
				{
					$dont = false;
					try {
						Dao::execSql($sql);
					} catch (Exception $e)
					{
						echo $sql."\n";
						echo $e->getMessage()."\n";
						$dont = true;
					}
					
					if($echoImport && !$dont)
						echo $sql."\n";
		
				}
				
				
			}

		}
	}
}
?>