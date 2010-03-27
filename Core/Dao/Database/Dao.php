<?php

class Dao
{
	/**
	 * PDO Database Connection
	 *
	 * @var PDO
	 */
	protected $database_connection;
	protected $ip;
	protected $database;
	protected $username;
	protected $password;
	
	protected $totalRows = 0;
	public static $debug = false;
	
	public function __construct($ip=null,$database=null,$username=null,$password=null)
	{	
		$this->ip = $ip == null ? Config::get("database","server") : $ip;
		$this->database = $database == null ? Config::get("database","database") : $database;
		$this->username = $username == null ? Config::get("database","username") : $username;
		$this->password = $password == null ? Config::get("database","password") : $password;
		$this->connect();
	}
	
	public function connect()
	{
		$this->database_connection = new PDO("mysql:host=".$this->ip.";dbname=".$this->database, $this->username, $this->password);
		$this->database_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function execute(BaseStatement $statement)
	{
		$sql = $statement->toSQL();
		
		if(self::$debug === true)
			echo $sql."\n";
			
		$query = $this->database_connection->prepare($sql);
		try {
			$query->execute(array());
		} catch(PDOException $e)
		{
			if(self::$debug === true)
			{	
				echo '<table border="1" width="100%"><tr><td><pre>';
				echo $sql;
				echo '</pre></td></tr></table>';
				die();
			} else {
				throw $e;				
			}
		}
		
		if($statement instanceof Insert)
			$statement->setExecutionResult($this->database_connection->lastInsertId());
		if($statement instanceof GenericSelect || ($statement instanceof SqlStatement && $statement->getDoResults()))
			$statement->setExecutionResult($query->fetchAll(PDO::FETCH_ASSOC));
		
		if($statement->getCalculateTotalRows())
		{
			$sql = new SqlStatement();
			$sql->setDoResults(true);
			$sql->setSQL('SELECT FOUND_ROWS();');
			$this->execute($sql);
			$results = $sql->getResultSet();
			$this->totalRows = $results[0]['FOUND_ROWS()'];					
		}
	}
	
	/**
	 * getter TotalRows
	 *
	 * @return int
	 */
	public function getTotalRows()
	{
		return $this->totalRows;
	}
}

?>