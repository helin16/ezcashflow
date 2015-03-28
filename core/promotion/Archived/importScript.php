<?php
require(dirname(__FILE__) . '/../main/bootstrap.php');

class ImportScript
{
	private static $_pdo = null;
	/**
	 * return the PDO
	 */
	private static function _connect()
	{
		if(!self::$_pdo instanceof PDO) {
			try
			{
				// DSN FORMAT: "mysql:host=localhost;dbname=test"
				self::$_pdo = new PDO('mysql:host=localhost;dbname=ezcash', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				self::$_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch (PDOException $e)
			{
				throw new DaoException("Error (Dao::connect): " . $e->getMessage());
			}
		}
		return self::$_pdo;
	}
	/**
	 * Run an SQL statement and return the PDOStatement object
	 *
	 * @param string $sql          The sql statement
	 * @param array  $params       The parameters for the sql
	 * @param int    $lastInsertId The id of the last insert sql
	 *
	 * @return PDOStatement
	 */
	private static function _execSql($sql, array $params = array(), &$lastInsertId = null)
	{
		self::_connect();
		$stmt = self::$_pdo->prepare($sql);
		$flattenSql = $stmt->queryString . '. With Params(size=' . count($params) . '): ' .  print_r($params, true);
		try
		{
			$stmt->execute($params);
			$retVal = self::$_pdo->lastInsertId();
			if(is_numeric($retVal) && $retVal > 0)
				$lastInsertId = $retVal;
		}
		catch (Exception $ex)
		{
			//             echo '<pre>';
			//             $stmt->debugDumpParams();
			//             die();
			throw new DaoException("Sql error(" . $ex->getMessage() . "): " . $flattenSql);
		}
		return $stmt;
	}
	public static function run()
	{
		$accMap = array();
		self::_importAccountEntries($accMap);
// 		self::_importTrans($accMap);
	}

	private static function _importAccountEntries(&$accMap = array(), $parentId = null, AccountEntry $parent = null,  Organization $org = null)
	{
		$sql = 'select * from accountentry where ' . ($parentId === null ? 'parentId = 0' : ' parentId = ' . $parentId) . ' order by accountNumber asc';
		$result = self::_execSql($sql)->fetchAll(PDO::FETCH_ASSOC);
		$org = $org instanceof Organization ? $org : Organization::get(1024);
		foreach($result as $row)
		{
			if(!$parent instanceof AccountEntry)
				$accountEntry = AccountEntry::createRootAccount($org, $row['name'], AccountType::get(substr($row['accountNumber'], 0, 1)), intval($row['allowTrans']) === 0, $row['value'], $row['comments']);
			else
				$accountEntry = AccountEntry::create($org, $parent, $row['name'], intval($row['allowTrans']) === 0, $row['value'], $row['comments']);
			if(intval($row['active']) === 0) {
				$accountEntry->setActive(false)
					->save();
			}
// 			var_dump($row['id'] . '=>' . $accountEntry->getId() );
			$accMap[$row['id']] = $accountEntry->getId();
			self::_importAccountEntries($accMap, $row['id'], $accountEntry, $org);
		}
	}

	private static function _importTrans(array $accMap)
	{
		$sql = 'select * from transaction where active = 1';
		$result = self::_execSql($sql)->fetchAll(PDO::FETCH_ASSOC);
// 		echo 'Got ' . count($result) . ' rows  => ';
		foreach($result as $index => $row)
		{
			if(isset($accMap[$row['fromId']]) && isset($accMap[$row['toId']])) {
				$fromAcc = AccountEntry::get($accMap[$row['fromId']]);
				$toAcc = AccountEntry::get($accMap[$row['toId']]);
				if(in_array(intval($fromAcc->getType()->getId()), array(AccountType::ID_ASSET, AccountType::ID_LIABILITY)) && intval($toAcc->getType()->getId()) === AccountType::ID_EXPENSE) { //expense
					Transaction::create($fromAcc, new UDate($row['created']), $row['value'], null, $row['comments']);
					Transaction::create($toAcc, new UDate($row['created']), null, $row['value'], $row['comments']);
				}
				else if(in_array(intval($fromAcc->getType()->getId()), array(AccountType::ID_ASSET, AccountType::ID_LIABILITY)) && in_array(intval($toAcc->getType()->getId()), array(AccountType::ID_ASSET, AccountType::ID_LIABILITY))) { //transfer
// 					Transaction::create($fromAcc, new UDate($row['created']), $row['value'], null, $row['comments']);
// 					Transaction::create($toAcc, new UDate($row['created']), null, $row['value'], $row['comments']);
				}
			} else if(isset($accMap[$row['toId']])) {
// 				$toAcc = AccountEntry::get($accMap[$row['toId']]);
// 				Transaction::create($toAcc, new UDate($row['created']), $row['value'], null, $row['comments']);
			}
		}
// 		echo 'finished ' . $index + 1 . ' rows';
	}
}
echo '<pre>';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
ImportScript::run();