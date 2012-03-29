<?php
class Aspect_JoinPoint_Regex_ClassFunction
    extends Aspect_JoinPoint_Regex
{
    private $_funcRegex;
    private $_classRegex;
    public function  __construct($classRegex, $funcRegex)
    {
        $this->_funcRegex  = $funcRegex;
        $this->_classRegex = $classRegex;
    }
    public function  test($class, $function, $instance)
    {
        return
            $this->_matchRegex($this->_classRegex, $class)
                && $this->_matchRegex($this->_funcRegex, $function);
    }
}
