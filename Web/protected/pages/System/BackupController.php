<?php
/**
 * This is the backup/restore page
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class BackupController extends EshopPage  
{
    /**
     * which command to mysql store
     * 
     * @var string
     */
	private $_mysql ='mysql';
	/**
	 * which command to to mysql back
	 * 
	 * @var string
	 */
	private $_mysqldump = "mysqldump";
	/**
	 * Where to save the mysql backup
	 * 
	 * @var string
	 */
	private $_backFile = "contents/download/backup.sql";
	/**
	 * where to store the .sql file
	 * 
	 * @var string
	 */
	private $_restoreFile = "contents/download/backup_restore.sql";
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{}
	/**
	 * Event: saving the backup
	 * 
	 * @param TButtn $sender The event sender
	 * @param Mixed  $param  The event params
	 * 
	 * @return BackupController
	 */
	public function backup($sender,$param)
	{
		$this->backupResult->Text="";
		try
		{
			$host = trim(Config::get("Database","LoadBalancer"));
			$username = trim(Config::get("Database","Username"));
			$password = trim(Config::get("Database","Password"));
			$database = trim(Config::get("Database","CoreDatabase"));
			
			if(file_exists($this->_backFile))
				unlink($this->_backFile);
			if($password!="")
				$command =sprintf('%s -h%s -u%s -p\'%s\' %s > %s',$this->_mysqldump,$host,$username,$password,$database,$this->_backFile);
			else
				$command =sprintf('%s -h%s -u%s %s > %s',$this->_mysqldump,$host,$username,$database,$this->_backFile);
			system($command,$return);
			$this->backupResult->Text="<div style='width:300px;margin:10px;padding:15px;background:#cccccc;border: 1px #ff0000 dotted;'><h3>Result:$return</h3>Backup generated. <a href='{$this->_backFile}' target='__blank'>click here to download</a></div>";
		}
		catch (Exception $ex)
		{
			$this->backupResult->Text=$ex->getMessage();
		}
		return $this;
	}
	/**
	 * Event: uploaded a file
	 * 
	 * @param TFileUploader $sender The event sender
	 * @param Mixed         $param  The event params
	 * 
	 * @return BackupController
	 */
	public function fileUploaded($sender,$param)
	{
		$this->restoreResult->Text = "";
		if($sender->HasFile)
        {
        	$result = "<div style='width:300px;margin:10px;padding:15px;background:#cccccc;border: 1px #ff0000 dotted;'>";
        	try{
        		if(file_exists($this->_restoreFile))
					unlink($this->_restoreFile);
        		$sender->saveAs($this->_restoreFile,true);
        		$result.="You just uploaded a file: {$sender->FileName} <br/>Size: {$sender->FileSize}<br/>  Type: {$sender->FileType}";
        	}
        	catch(Exception $ex)
        	{
        		$result .=$ex->getMessage();
        	}
        	$result .= "</div>";
        	$this->restoreResult->Text = $result;
        }
        return $this;
	}
	/**
	 * Event: restoring mysql from a file
	 * 
	 * @param TButton $sender The event sender
	 * @param Mixed   $param  The event params
	 * 
	 * @return BackupController
	 */
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
				$command =sprintf('%s -h%s -u%s -p\'%s\' %s < %s',$this->_mysql,$host,$username,$password,$database,$this->_restoreFile);
			else
				$command =sprintf('%s -h%s -u%s %s < %s',$this->_mysql,$host,$username,$database,$this->_restoreFile);
			system($command,$return);
			$result .= "<h3>Result:$return</h3>restored from this file. <a href='{$this->_restoreFile}' target='__blank'>click here to download</a>";
		}
		catch(Exception $ex)
        {
        	$result .=$ex->getMessage();
        }
		$result .= "</div>";
		$this->restoreResult->Text = $result;
		return $this;
	}
}
?>