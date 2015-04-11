<?php
require_once dirname(__FILE__) . '/../main/bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
foreach(Transaction::getAll(false) as $transtion)
{
	try{
		Dao::beginTransaction();
		$transtion->setUpdated($transtion->getUpdated())
			->setUpdatedBy($transtion->getUpdatedBy())
			->save();
		Dao::commitTransaction();
	} catch (Exception $e) {
		Dao::rollbackTransaction();
		var_dump($e->getMessage());
		var_dump($e->getTraceAsString());
	}
}