<?php
class Demo_Logging
{
	private static $_logAspect = array(null, null);
	//Dynamically Enable Logging
	public static function enableLogging()
	{
		//Create hook on everything
		$h = new Aspect_Hook(array(
				    new Aspect_JoinPoint_True
			    ));

		self::$_logAspect[0] = new Aspect_Function($h, 'Demo_Logging::logBefore');
		self::$_logAspect[1] = new Aspect_Function($h, 'Demo_Logging::logAfter');
		Aspect_Handler::wrapBefore(self::$_logAspect[0]);
		Aspect_Handler::wrapAfter(self::$_logAspect[1]);
	}
	//Dynamically Disable Logging
	public static function disableLogging()
	{
		if(self::$_logAspect === array(null, null)) return;
		Aspect_Handler::unwrapBefore (self::$_logAspect[0]);
		Aspect_Handler::unwrapAfter(self::$_logAspect[1]);
		self::$_logAspect = array(null, null);
	}
	//Dynamically Enable Logging
	public static function logBefore($meta) { self::_log($meta, "[Before] > ", func_get_args()); }
	public static function logAfter($meta)	{ self::_log($meta, "[After] > ", func_get_args()); }

	//Our log Formatting function
	private static function _log($meta, $prefix, $args2)
	{
		//Data provided by the Meta
		$func	= $meta->getFunction();
		$args	= $meta->getArgs();
		$class	= $meta->getClass();
		$return	= $meta->getReturn();
		//Remove Meta from args
		array_shift($args2);
		
		echo "$prefix $class.$func(".implode(", ", $args2).") orig-args: (".implode(", ", $args).") ->  ";
		if(is_object($return))
			if($return instanceof Aspect_Wrapper)
				echo "{Wrapped} ". $return->__get_class();
			else
				echo get_class($return);
		else
		{
			if($return === null) $return = "Null";
			echo $return;
		}

		echo "\n";
	}
}
