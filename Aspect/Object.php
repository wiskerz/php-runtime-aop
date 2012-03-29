<?php
class Aspect_Object extends Aspect_Abstract
{

    private $_obj;
    private $_method;
    public function __construct($sequence, $object, $method)
    {
        parent::__construct($sequence);
        $this->_obj     = $object;
        $this->_method  = $method;
    }
    public function  fire($class, $function, $args, $instance = null)
    {
        return call_user_func_array(array(&$this->_obj, $this->_method), $args);
    }
}