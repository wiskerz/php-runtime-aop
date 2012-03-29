<?php
class Aspect_JoinPoint_True
    extends Aspect_JoinPoint_Abstract
{
    public function  test($class, $function, $instance)
    {
        return true;
    }
}
