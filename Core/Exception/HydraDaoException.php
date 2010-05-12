<?php

/**
 * @package Core
 * @subpackage Exception
 */
class HydraDaoException extends Exception
{
	public function __construct($message)
	{
		// Supply the base exception class with an arbitrary code value
		parent::__construct($message, 0);
	}
}

?>