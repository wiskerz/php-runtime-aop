<?php
class Aspect_Result
{
    /**
     * @var array
     */
    private $_replace;
    /**
     * @var value
     */
    private $_stop;

    private $_return;

    public function __construct($stop, $return,  $replace = array())
    {
        $this->_stop    = $stop;
        $this->_replace = $replace;
        $this->_return  = $return;
    }
    final public function stop()
    {
        return $this->_stop;
    }
    final public function stopValue()
    {
        return $this->_return;
    }
    final public function replace()
    {
        return $this->_replace;
    }
}
