<?php
/**
 * @author Tony Hokayem
 */
/**
 * Use to trigger a PHP function or an AOP aware function
 * First argument is an Aspect_Meta object
 * Supports static calls "Classname::func"
 * @author Tony Hokayem
 * @see Aspect_Meta
 */
class Aspect_Function extends Aspect_Abstract
{
	/**
	 * Function to call
	 * @var string
	 */
    private $_method;
	/**
	 * @param Aspect_Hook $sequence Match Rules
	 * @param string $fname Function Name (to delegate)
	 */
    public function __construct($sequence, $fname)
    {
        parent::__construct($sequence);

		$this->_method = $fname;
    }
    public function  fire($class, $function, $args, $instance = null)
    {
        return call_user_func_array($this->_method, $args);
    }
}