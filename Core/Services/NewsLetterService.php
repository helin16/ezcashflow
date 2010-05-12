<?php
class NewsLetterService extends BaseService 
{
	public function __construct()
	{
		parent::__construct("Subscriber");
	}
	
	public function subscribe($email)
	{
		$now = new HydraDate();
		$subscribers = $this->findByCriteria("email like '$email'",false);
		if(count($subscribers)>0)
		{
			if($subscribers[0]->getActive()==true)
				throw new Exception("Email exists already!");
			else
			{
				$sql ="update subscriber set active=1,updated='$now',updatedById=1 where `id`='{$subscribers[0]->getId()}'";
				Dao::execSql($sql);	
				return;
			}
		}
		
		$langId = Core::getPageLanguage()->getId();
		$sql ="insert into subscriber(`email`,`key`,`isConfirmed`,`languageId`,`created`,`createdById`,`updated`,`updatedById`)
				value('$email','".md5("$email $now")."','1','$langId','$now','1','$now','1')";
		Dao::execSql($sql);
	}
	
	public function unsubscribe($key)
	{
		$now = new HydraDate();
		$sql ="update subscriber set active=0,updated='$now',updatedById=1 where `key`='$key'";
		Dao::execSql($sql);
	}
}
?>