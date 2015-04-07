<?php
require_once dirname(__FILE__) . '/../main/bootstrap.php';

foreach(Transaction::getAll() as $transtion)
{
	$transtion->setUpdated($transtion->getUpdated())
		->setUpdatedBy($transtion->getUpdatedBy())
		->save();
}