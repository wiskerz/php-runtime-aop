<?php
/**
 * @author Tony Hokayem
 */
/**
 * Use to trigger a call to an object by mirroring
 * the triggering object
 * If the triggering object calls a function func
 * Then the function prefix + func will be called on the
 * given object
 * @author Tony Hokayem
 */
class Aspect_OneToOne extends Aspect_Abstract
{
	/**
	 * Instance of an object
	 * @var mixed
	 */
	private $_obj;
	/**
	 * Prefix for transformation of function calls
	 * @var string
	 */
	private $_prefix;
	/**
	 *
	 * @param Aspect_Hook $sequence Match Rules
	 * @param <type> $object
	 * @param <type> $prefix
	 */
	public function __construct($sequence, $object, $prefix = "")
	{
		parent::__construct($sequence);
		$this->_obj     = $object;
		$this->_prefix	= $prefix;
	}
	public function  fire($class, $function, $args, $instance = null)
	{
		return call_user_func_array(array(&$this->_obj,
					$this->_prefix . $function), $args
				);
	}
}
