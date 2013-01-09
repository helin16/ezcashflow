<?php
/**
 * The Dao Exception
 * 
 * @package    Core
 * @subpackage Exception
 * @author     lhe<helin16@gmail.com>
 */
class DaoException extends Exception
{
	public function __construct($message)
	{
		// Supply the base exception class with an arbitrary code value
		parent::__construct($message, 0);
	}
}

?>