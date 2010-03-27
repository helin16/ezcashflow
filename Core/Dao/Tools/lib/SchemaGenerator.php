<?php

class SchemaGenerator
{
	/**
	 * @var Dao
	 */
	private $dao;
	/**
	 * @var DatabaseGenerator
	 */
	private $databaseGenerator;
	
	public $debug = false;
	
	public function __construct($path,$server,$database,$username,$password)
	{
		$this->databaseGenerator = new DatabaseGenerator($path,$database,"MyISAM","utf8");
		$this->dao = new Dao($server,$database,$username,$password);
	}
	
	public function build()
	{
		$drops = $this->databaseGenerator->generateDrop();
		$creates = $this->databaseGenerator->generateCreate();
		
		if(sizeof($drops) != sizeof($creates))
			throw new Exception("Size of Create and Drop Statements is not equal.");
		
		
		$sql = new SqlStatement();
		for($i=0;$i<sizeof($drops);$i++)
		{
			if($this->debug)
				echo $drops[$i];
			$sql->setSQL($drops[$i]);
			$this->dao->execute($sql);
			
			if($this->debug)
				echo $creates[$i];
			$sql->setSQL($creates[$i]);
			$this->dao->execute($sql);
		}		
	}
	
	public function importSql($sqlFilePath,$echoDropCreate=false,$echoImport=false)
	{
		if($sqlFilePath != null)
		{
			if($echoImport)
				echo "\n\nImporting Data ($sqlFilePath)\n";

			try 
			{
				$data = explode("\n",file_get_contents($sqlFilePath));
			} 
			catch (Exception $e)
			{
				echo "File \"$importFile\" Failed to open.\n";
				return; 	
			}
			
			$sqlStatement = new SqlStatement();
			foreach($data as $row)
			{
				$sql = trim($row);
				if(strlen($sql) > 0)
				{
					$dont = false;
					try 
					{
						$sqlStatement->setSQL($row);
						$this->dao->execute($sqlStatement);
					} catch (Exception $e)
					{
						echo "\n<b>Error For:$row</b>\n";
						echo $e->getMessage()."\n\n";
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