<?php
class Aspect_JoinPoint_Instanceof
    extends Aspect_JoinPoint_Abstract
{
    private $_class;
    public function __construct($class)
    {
        $this->_class = $class;
    }
    public function  test($class, $function, $instance)
    {
        return ($instance instanceof $this->_class);
    }
}
