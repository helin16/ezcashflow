<?php

/**
 * @package Core
 * @subpackage Exception
 */
class HydraEntityValidationException extends Exception
{
	public function __construct($message)
	{
		// If the input is an array, convert it to a string of errors
		if (is_array($message))
		{
			$message = implode(", ", $message);
		}
		
		// Supply the base exception class with an arbitrary code value
		parent::__construct($message, 0);
	}
}

?>