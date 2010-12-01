<?php
class BackupController extends EshopPage  
{
	private $mysqldump = "C:\wamp\bin\mysql\mysql5.0.51b\bin\mysqldump";
	private $mysql = "C:\wamp\bin\mysql\mysql5.0.51b\bin\mysql";
//	private $mysql ='mysql';
//	private $mysqldump = "mysqldump";
	private $backFile = "contents/download/backup.sql";
	private $restoreFile = "contents/download/backup_restore.sql";
	
	public function onLoad($param)
	{
		if(!$this->IsPostBack)
		{
		}
	}
	
	public function backup($sender,$param)
	{
		$this->backupResult->Text="";
		try
		{
			$host = trim(Config::get("Database","LoadBalancer"));
			$username = trim(Config::get("Database","Username"));
			$password = trim(Config::get("Database","Password"));
			$database = trim(Config::get("Database","CoreDatabase"));
			
			if(file_exists($this->backFile))
				unlink($this->backFile);
			if($password!="")
				$command =sprintf('%s -h%s -u%s -p\'%s\' %s > %s',$this->mysqldump,$host,$username,$password,$database,$this->backFile);
			else
				$command =sprintf('%s -h%s -u%s %s > %s',$this->mysqldump,$host,$username,$database,$this->backFile);
			system($command,$return);
			$this->backupResult->Text="<div style='width:300px;margin:10px;padding:15px;background:#cccccc;border: 1px #ff0000 dotted;'><h3>Result:$return</h3>Backup generated. <a href='{$this->backFile}' target='__blank'>click here to download</a></div>";
		}
		catch (Exception $ex)
		{
			$this->backupResult->Text=$ex->getMessage();
		}
	}
	
	public function fileUploaded($sender,$param)
	{
		$this->restoreResult->Text = "";
		if($sender->HasFile)
        {
        	$result = "<div style='width:300px;margin:10px;padding:15px;background:#cccccc;border: 1px #ff0000 dotted;'>";
        	try{
        		if(file_exists($this->restoreFile))
					unlink($this->restoreFile);
        		$sender->saveAs($this->restoreFile,true);
        		$result.="You just uploaded a file: {$sender->FileName} <br/>Size: {$sender->FileSize}<br/>  Type: {$sender->FileType}";
        	}
        	catch(Exception $ex)
        	{
        		$result .=$ex->getMessage();
        	}
        	$result .= "</div>";
        	$this->restoreResult->Text = $result;
        }
	}
	
	public function restore($sender,$param)
	{
		$result = "<div style='width:300px;margin:10px;padding:15px;background:#cccccc;border: 1px #ff0000 dotted;'>";
		try
		{
			$host = trim(Config::get("Database","LoadBalancer"));
			$username = trim(Config::get("Database","Username"));
			$password = trim(Config::get("Database","Password"));
			$database = trim(Config::get("Database","CoreDatabase"));
			
			if($password!="")
				$command =sprintf('%s -h%s -u%s -p\'%s\' %s < %s',$this->mysql,$host,$username,$password,$database,$this->restoreFile);
			else
				$command =sprintf('%s -h%s -u%s %s < %s',$this->mysql,$host,$username,$database,$this->restoreFile);
			system($command,$return);
			$result .= "<h3>Result:$return</h3>restored from this file. <a href='{$this->restoreFile}' target='__blank'>click here to download</a>";
		}
		catch(Exception $ex)
        {
        	$result .=$ex->getMessage();
        }
		$result .= "</div>";
		$this->restoreResult->Text = $result;
	}
}
?>