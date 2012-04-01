<?php
/**
 * @author Tony Hokayem
 */
/**
 * Use to trigger a call to an object.method()
 * First argument is an Aspect_Meta object
 * @author Tony Hokayem
 * @see Aspect_Meta
 */
class Aspect_Object extends Aspect_Abstract
{
	/**
	* Object Instance
	* @var mixed
	*/
	private $_obj;
	/**
	* Method to call on object instance
	* @var string
	*/
	private $_method;
	/**
	*
	* @param Aspect_Hook $sequence Hook
	* @param mixed $object Instance of an object
	* @param string $method Method to call on the object
	*/
	public function __construct($sequence, $object, $method)
	{
		parent::__construct($sequence);
		$this->_obj     = $object;
		$this->_method  = $method;
	}
	public function  fire($class, $function, $args, $instance = null)
	{
		return call_user_func_array(
					array(&$this->_obj, $this->_method), $args
				);
	}
}