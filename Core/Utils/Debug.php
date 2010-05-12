<?php
class Debug
{
	public static function inspect($param)
	{
		echo "<pre>";
			var_dump($param);
		echo "</pre>";
	}
}
?>