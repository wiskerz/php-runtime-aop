<?php
class Aspect_Function extends Aspect_Abstract
{

    private $_method;
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