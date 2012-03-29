<?php
class Aspect_Result_Stop
    extends Aspect_Result
{
    public function __construct($value = null)
    {
        parent::__construct(true, $value, array());
    }
}