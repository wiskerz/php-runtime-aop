<?php
class Aspect_JoinPoint_Regex_Function
    extends Aspect_JoinPoint_Regex
{
    public function  test($class, $function, $instance)
    {
        return $this->_testRegex($function);
    }
}
