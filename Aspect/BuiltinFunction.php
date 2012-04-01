<?php
/**
 * @author Tony Hokayem
 */
/**
 * Use to trigger a PHP function or a non-AOP aware function
 * Supports static calls "Classname::func"
 * @author Tony Hokayem
 */
class Aspect_BuiltinFunction extends Aspect_Abstract
{
	/**
	 * Function to call
	 * @var string
	 */
	private $_method;
	/**
	 * @param Aspect_Hook $sequence
	 * @param string $fname Function Name (to delegate)
	 */
	public function __construct($sequence, $fname)
	{
		parent::__construct($sequence);
		if(!function_exists($fname))
			throw new Exception("Function $fname not defined");

		$this->_method = $fname;
	}
	public function  fire($class, $function, $args, $instance = null)
	{
		$args = array_shift($args); //Remove Aspect meta-object
		return call_user_func_array($this->_method, $args);
	}
}