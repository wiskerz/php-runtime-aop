<?php
class Aspect_Wrapper
{
    private $_obj;

    public function __instanceof($class)
    {
        return $this->_obj instanceof $class;
    }
    public function __get_class()
    {
        return get_class($this->_obj);
    }
    public static function load($class)
    {
        $args   = func_get_args();
        array_shift($args);

        $x = new ReflectionClass($class);

        $obj = Aspect_Handler::fire('newInstance', $args, $x, true);

        $obj = Aspect_Handler::fire('wrapInstance', $args, $obj, false);

        if($obj)
            return ($obj);
        else
            return null;
    }
    public static function wrap($obj, $args = array())
    {
        $r = new self($obj);
        if($obj instanceof Aspect_Aware)
        {
           $obj->__Aware_setWrapper($r);
           @call_user_func_array(array($obj, '__Aware_onWrap'), $args);
        }
        return $r;
    }
    private function __construct($obj)
    {
        $this->_obj = $obj;
    }
    public function __call($name, $args)
    {
        if(!is_array($args))
            $args = array($args);
        return Aspect_Handler::fire($name, $args, $this->_obj);
    }
    public function __invoke($args)
    {
        if(!is_array($args))
            $args = array($args);
        return Aspect_Handler::fire('__invoke', $args, $this->_obj);
    }
    public function __set($var, $value)
    {
        return Aspect_Handler::fire('__set', array($var, $value), $this->_obj);
    }
    public function __get($var)
    {
        return Aspect_Handler::fire('__get', array($var), $this->_obj);
    }
    public function __toString()
    {
        return Aspect_Handler::fire('__toString', array(), $this->_obj);
    }
    /*
    //Needs revision
    public function __destruct()
    {
        return Aspect_Handler::fire('__destruct', array(), $this->_obj);
    }*/

}
