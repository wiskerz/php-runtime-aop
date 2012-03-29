<?php
class Aspect_Meta_Status
{
    private $_aborted = false;
    private $_return  = null;
    
    public function aborted()       { return (bool) $this->_aborted; }
    public function getReturn()     { return $this->_return; }


    public function setAbort()      { $this->_aborted = true; return $this; }
    public function clearAbort()    { $this->_aborted = false;return $this; }

    public function setReturn($ret)
    {
        $this->_return = $ret;
        return $this;
    }
}