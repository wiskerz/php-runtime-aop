<?php
abstract class Aspect_JoinPoint_Abstract
{

        abstract public function test($class, $function, $instance);
}
