<?php

class FileScanner
{
	protected $files;
	protected $entity;	
	
	protected function loadMeta()
	{
		foreach($this->files as $file)
		{
			require_once($file);
			$class = pathinfo($file);
			$this->entity = new $class['filename']();
			$this->entity->meta();
		}
	}
	
	public function __construct($path=null)
	{
		if(!is_dir($path))
			$path = dirname(__FILE__);
		
		$this->files = $this->getFiles($path,array('php'),array('.svn','Entity'));
		$this->loadMeta();		
	}
	
	/**
	 * Generates a List of Files for a Path Extension and Exclusion Pattern
	 * 
	 * @param string $path
	 * @param array(string) $extension
	 * @param array(string) $excluded
	 * @return string
	 */
	protected function getFiles($path,$extension = null,$excluded = null)
	{
		$files = array();
		$directory = scandir($path);

		if($extension == null)
		$extension = array(".*[tT]est.php");

		if($excluded == null)
		$excluded = array(".","..");
		else
		$excluded = array_merge($excluded,array(".",".."));

		foreach($directory as $file)
		{
			if(in_array($file,$excluded))
			continue;

			$pathFile = $path.'/'.$file;

			if(is_dir($pathFile) && is_readable($pathFile))
			{
				$files = array_merge($files,$this->getFiles($pathFile,$extension,$excluded));
			} else if(is_file($pathFile) && is_readable($pathFile))
			{
				foreach($extension as $regex)
				{
					if(ereg($regex,$pathFile))
					{
						$files[] = $pathFile;
					}
				}
					
			} else {
				throw new Exception("Something Bad! ".$file);
			}
		}

		return $files;
	}
}

?>