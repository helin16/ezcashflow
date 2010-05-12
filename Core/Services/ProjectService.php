<?php
class ProjectService extends BaseService 
{
	public function __construct()
	{
		parent::__construct("Project");
	}
	
	public function getProjectByTitle($title,$searchActiveOnly=true,$page = null,$pagesize = 30)
	{
		$title = strtoupper(str_replace(" ","_",trim($title)));
		$contents = $this->findByCriteria("ucase(replace(trim(`title`),' ','_')) = '$title'",$searchActiveOnly,$page,$pagesize);
		return $contents;
	}
}
?>