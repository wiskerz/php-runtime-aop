<?php
class Aspect_Result_Replace
    extends Aspect_Result
{
    public function __construct($replace = array())
    {
       parent::__construct(false, false, $replace);
    }
}