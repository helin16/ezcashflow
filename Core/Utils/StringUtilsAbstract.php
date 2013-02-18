<?php
/**
 * StringUtilsAbstract
 *
 * @package    Core
 * @subpackage Utils
 * @author     lhe<helin16@gmail.com>
 */
abstract class StringUtilsAbstract
{
    /**
     * convert the first char into lower case
     * 
     * @param Role $role The role
     */
	public static function lcFirst($string)
	{
		return strtolower(substr($string, 0, 1)) . substr($string, 1);
	}
}

?>