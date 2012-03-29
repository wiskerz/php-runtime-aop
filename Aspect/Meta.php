<?php
class Aspect_Meta
{
    private $_class;
    private $_function;
    private $_instance;
    /**
     *
     * @var Aspect_Meta_Status
     */
    private $_status;
    private $_args;

    public function __construct($class, $func, $inst, $sObject, $args)
    {
        $this->_class       = $class;
        $this->_function    = $func;
        $this->_instance    = $inst;
        $this->_status      = $sObject;
        $this->_args        = $args;
    }
    
    public function getClass()      { return $this->_class;     }
    public function getFunction()   { return $this->_function;  }
    public function getInstance()   { return $this->_instance;  }

    public function aborted()       { return $this->_status->aborted();   }
    public function getReturn()     { return $this->_status->getReturn(); }
    public function getArgs()       { return $this->_args; }

}
