<?php
class Aspect_BuiltinFunction extends Aspect_Abstract
{

    private $_method;
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