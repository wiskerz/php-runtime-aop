<?php
class Aspect_OneToOne extends Aspect_Abstract
{
    private $_obj;
    private $_prefix;
    public function __construct($sequence, $object, $prefix = "")
    {
        parent::__construct($sequence);
        $this->_obj     = $object;
	$this->_prefix	= $prefix;
    }
    public function  fire($class, $function, $args, $instance = null)
    {
        return call_user_func_array(array(&$this->_obj,
            $this->_prefix . $function), $args);
    }
}
