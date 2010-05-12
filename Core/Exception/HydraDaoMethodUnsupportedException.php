<?php

/**
 * @package Core
 * @subpackage Exception
 */
class HydraDaoMethodUnsupportedException extends Exception
{
	public function __construct($message)
	{
		// Supply the base exception class with an arbitrary code value
		parent::__construct($message . ' is unsupported by this DAO', 0);
	}
}

?>