<?php
class LanguageService extends BaseService 
{
	public function __construct()
	{
		parent::__construct("PageLanguage");
	}
	
	/**
	 * @param unknown_type $code
	 * @return PageLanguage
	 */
	public function findByCode($code)
	{
		$results = $this->findByCriteria("code='$code'",true);
		return isset($results[0]) ? $results[0] : null;
	}
}
?>