<?php
set_include_path("." . PATH_SEPARATOR . get_include_path());
function autoLoad($class)
{
   include_once str_replace("_", "/", $class) . ".php";
}

spl_autoload_register('autoLoad');
