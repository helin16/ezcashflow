<script type='text/javascript' >
	function showHideDiv(divId,button)
	{
		var div = document.getElementById(divId);
		if(div.style.display=='none')
		{
			div.style.display='block';
			button.value='Hide';
		}
		else
		{
			div.style.display='none';
			button.value='Show';
		}
	}
</script>
<?php

require_once(dirname(__FILE__)."/../../bootstrap.php");

$errorLevel = 0;
$defaults = array(	"Path"=>dirname(__FILE__)."/../../Entity/",
					"Server"=>"",
					"Database"=>"",
					"Username"=>"",
					"Password"=>"",
					"SampleDataFilePath"=>dirname(__FILE__)."/sampleData.sql",
					"NeedToImportData"=>""
				);


function check($string)
{
	global $errorLevel;
	global $defaults;
	if(!isset($_GET[$string]))
	{
		echo "Variable \"$string\" is not set on GET, please correct and retry.<br/>";
		$errorLevel++;
		return $defaults[$string];
	} else
		return $_GET[$string];
}

$path = check("Path");
$server = check("Server");
$database = check("Database");
$username = check("Username");
$password = check("Password");
$sampleDataFilePath = check("SampleDataFilePath");
$needToImportData = check("NeedToImportData");

if($errorLevel != 0)
{
	echo '<form>';
	echo 'Path: <input type="text" name="Path" Value="'.$path.'" /><br/>';
	echo 'Server: <input type="text" name="Server" Value="'.$server.'" /><br/>';
	echo 'Database: <input type="text" name="Database" Value="'.$database.'" /><br/>';
	echo 'Username: <input type="text" name="Username" Value="'.$username.'" /><br/>';
	echo 'Password: <input type="text" name="Password" Value="'.$password.'" /><br/>';
	echo 'SampleDataFilePath: <input type="text" name="SampleDataFilePath" Value="'.$sampleDataFilePath.'" /><br/>';
	echo 'Import Data?<Select name="NeedToImportData">';
		echo '<option value="N"'.(strtoupper(trim($needToImportData))=="N" ? "selected": "").">No</option>";
		echo '<option value="Y"'.(strtoupper(trim($needToImportData))=="Y" ? "selected": "").">Yes</option>";
	echo '</Select><br />';
	echo '<input type="submit" value="Submit" />';
	echo '</form>';	
} 
else 
{
	echo "Generating Sample Data<br />
		======= 
		<input type='button' OnClick='showHideDiv(\"creatingTablesPanel\",this);' Value='Hide' />
		<b>Creating Tables</b>
		======================================================================<br />
		<div Id='creatingTablesPanel' style='width:100%;height:700px;overflow:auto;border:2px solid #eeeeee;'\">
			<pre>
		";
//	$dbGen = new SchemaGenerator($path,$server,$database,$username,$password);
	
	$var = new SchemaGenerator(dirname(__FILE__)."/../../Entity/", array(".svn"));
	$var->setupDatabase($sampleDataFilePath,true,true);
	echo "	</pre>";
	echo "</div>";
	
	if(strtoupper(trim($needToImportData))=="Y")
	{
		echo "<br />=======
			<input type='button' OnClick='showHideDiv(\"importingDataPanel\",this);' Value='Show' />
			<b>Importing Sample Data from '$sampleDataFilePath'</b>
			=================<br />
			<div Id='importingDataPanel' style='width:100%;height:700px;overflow:auto;border:2px solid #eeeeee;display:none;'\">
				<pre>";
//		$dbGen->importSql($sampleDataFilePath,false,true);
		echo "	</pre>";
		echo "==downloading data from WWW===<br />";
		echo "</div>";
	}
	echo 'Complete.<br/>';
}

?>
