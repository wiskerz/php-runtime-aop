<?php
/**
 * @author Tony Hokayem
 */
/**
 * Core functionality
 * Responsible for hooking and firing all aspects
 *
 * Provides the publish/subscribe mechanism
 *
 * @author Tony Hokayem
 */
class Aspect_Handler
{
	const CHAIN_PRE1    =   1;
	const CHAIN_PRE2    =   2;
	const CHAIN_POST1   =   3;
	const CHAIN_POST2   =   4;

	/**
	* Lookup table for global hooks
	* @see Aspect_Abstract
	* @var array
	*/
	private static $_global     = array(
		self::CHAIN_PRE1 => array(),
		self::CHAIN_PRE2 => array(),
		self::CHAIN_POST1 => array(),
		self::CHAIN_POST2 => array()
	);
	/**
	* Simple match: Lookup table for class-specific hooks
	* @see Aspect_Abstract
	* @var array
	*/
	private static $_perclass   = array(
		self::CHAIN_PRE1 => array(),
		self::CHAIN_PRE2 => array(),
		self::CHAIN_POST1 => array(),
		self::CHAIN_POST2 => array()
	);
	/**
	* Simple match: Lookup table for function-specific hooks
	* @see Aspect_Abstract
	* @var array
	*/
	private static $_perfunc    = array(
		self::CHAIN_PRE1 => array(),
		self::CHAIN_PRE2 => array(),
		self::CHAIN_POST1 => array(),
		self::CHAIN_POST2 => array()
	);

	/**
	* (Subscribe) Attach an advice to the bind location Before
	* Context: Read-only
	* @param Aspect_Abstract $aspect Advice to attach
	* @return bool Success
	*/
	public static function wrapBefore($aspect)
	{
		return self::attachAspect($aspect,
				self::CHAIN_PRE1,
				$aspect->getKeyClasses(),
				$aspect->getKeyFunctions());
	}
	/**
	* (Subscribe) Attach an advice to the bind location AroundBefore
	* Context: Read/Write
	* Useful to modify context for arguments passed to the initial call
	* If the aspect's fire function returns Aspect_Result
	* Stopping will jump to bind point After
	* Stopping with a result assumes the function has returned the result provided
	* Replacing will override parameters to the initial call
	* @param Aspect_Abstract $aspect Advice to attach
	* @return bool Success
	*/
	public static function wrapAroundBefore($aspect)
	{
		return self::attachAspect($aspect,
				self::CHAIN_PRE2,
				$aspect->getKeyClasses(),
				$aspect->getKeyFunctions());
	}
	/**
	* (Subscribe) Attach an advice to the bind location AroundAfter
	* Context: Read/Write
	* Useful to modify context for arguments passed to the initial call
	* If the aspect's fire function returns Aspect_Result
	* Stopping will jump to bind point After
	* Stopping with a result replaces the actual return of the function itself
	* Replacing Arguments is usually not advised here
	* @param Aspect_Abstract $aspect Advice to attach
	* @return bool Success
	*/
	public static function wrapAroundAfter($aspect)
	{
		return self::attachAspect($aspect,
				self::CHAIN_POST1,
				$aspect->getKeyClasses(),
				$aspect->getKeyFunctions());
	}
	/**
	* (Subscribe) Attach an advice to the bind location After
	* Context: Read-only
	* @param Aspect_Abstract $aspect Advice to attach
	* @return bool Success
	*/
	public static function wrapAfter($aspect)
	{
		return self::attachAspect($aspect,
				self::CHAIN_POST2,
				$aspect->getKeyClasses(),
				$aspect->getKeyFunctions());
	}

	/**
	* (Unsubscribe) Detach a given aspect from the Before chain
	* Undo a wrapBefore
	* @see Aspect_Handler::wrapBefore
	* @param Aspect_Abstract $aspect
	* @return bool Success
	*/
	public static function unwrapBefore($aspect)
	{
		return self::detachAspect($aspect, self::CHAIN_PRE1);
	}
	/**
	* (Unsubscribe) Detach a given aspect from the AroundBefore chain
	* Undo a wrapAroundBefore
	* @see Aspect_Handler::wrapAroundBefore
	* @param Aspect_Abstract $aspect
	* @return bool Success
	*/
	public static function unwrapAroundBefore($aspect)
	{
		return self::detachAspect($aspect, self::CHAIN_PRE2);
	}
	/**
	* (Unsubscribe) Detach a given aspect from the AroundAfter chain
	* Undo a wrapAroundAfter
	* @see Aspect_Handler::wrapAroundAfter
	* @param Aspect_Abstract $aspect
	* @return bool Success
	*/
	public static function unwrapAroundAfter($aspect)
	{
		return self::detachAspect($aspect, self::CHAIN_POST1);
	}
	/**
	* (Unsubscribe) Detach a given aspect from the After chain
	* Undo a wrapAfter
	* @see Aspect_Handler::wrapAfter
	* @param Aspect_Abstract $aspect
	* @return bool Success
	*/
	public static function unwrapAfter($aspect)
	{
		return self::detachAspect($aspect, self::CHAIN_POST2);
	}

	private static function attachAspect($aspect, $chain, $class = array(),
			$function = array())
	{
		//Simple Match check for classes
		if(is_array($class))
			foreach($class as $c)
				self::attachAspectToClass($aspect, $chain, $c);
		//Simple Match check for functions
		if(is_array($function))
			foreach($function as $f)
			self::attachAspectToFunction($aspect, $chain, $f);

		//Attach to global ONLY if no classes or functions provided
		if(!$class && !$function)
			self::$_global[$chain][$aspect->getAspectId()] = $aspect;

		return true;
	}

	/**
	* @param Aspect_Abstract $aspect
	* @param int $chain Bind Location
	* @return bool Success
	*/
	private static function detachAspect($aspect, $chain)
	{
		$id			= $aspect->getAspectId ();
		$class		= $aspect->getKeyClasses();
		$function	= $aspect->getKeyFunctions();


		//Check for Simple Match Classes and remove
		if(is_array($class))
			foreach($class as $c)
				if(isset(self::$_perclass[$c])
					&& isset(self::$_perclass[$c][$chain][$id]))
				unset(self::$_perclass[$c][$chain][$id]);
		else
			return false;

		//Check for Simple Functions remove
		if(is_array($function))
			foreach($function as $f)
				if(isset(self::$_perfunc[$f])
					&& isset(self::$_perfunc[$f][$chain][$id]))
					unset(self::$_perfunc[$f][$chain][$id]);
				else
					return false;
		//Global detach
		if(!$class && !$function)
			if(isset(self::$_global[$chain][$id]))
				unset(self::$_global[$chain][$id]);

		return true;
	}
	private static function attachAspectToClass($aspect, $chain, $class)
	{
		if(!isset(self::$_perclass[$class]))
		{
			self::$_perclass[$class] = array(
				self::CHAIN_PRE1 => array(),
				self::CHAIN_PRE2 => array(),
				self::CHAIN_POST1 => array(),
				self::CHAIN_POST2 => array()
			);
		}
		self::$_perclass[$class][$chain][$aspect->getAspectId()] = $aspect;
		return true;
	}
	private static function attachAspectToFunction($aspect, $chain, $func)
	{
		if(!isset(self::$_perfunc[$func]))
		{
			self::$_perfunc[$func] = array(
				self::CHAIN_PRE1 => array(),
				self::CHAIN_PRE2 => array(),
				self::CHAIN_POST1 => array(),
				self::CHAIN_POST2 => array()
			);
		}
		self::$_perfunc[$func][$chain][$aspect->getAspectId()] = $aspect;
		return true;
	}

	/**
	* Check for Aspect_Result Stop/Args Replace
	* @todo Remove by ref, return: array($stop, $args)
	* @param Aspect_Result $return
	* @param mixed $args
	* @return boolean stop?
	*/
	private static function stopAndMerge($return, &$args)
	{
		if($return instanceof Aspect_Result)
		{
			$stop = $return->stop();
			$e = array_shift($args);

			$temp = $args;
			$rep  = $return->replace();
			foreach($temp as $key => $val)
			{
				if(isset($rep[$key]))
					$args[$key] = $rep[$key];
			}
			array_unshift($args, $e);
		}
		else
			$stop = false;

		return $stop;
	}
	/**
	 * (Publish) Call a method on an object and trigger needed bind points
	 * $isReflection is needed for a specific case, that of a constructor
	 *
	 * @param string $func Method to call
	 * @param array $args Arguments to pass
	 * @param mixed $instance Object Instance
	 * @param bool $isReflection is a reflection object?
	 * @return mixed Method's affected return
	 */
	public static function fire($func, $args, $instance, $isReflection = false)
	{
		if(!$isReflection)
			$class   = get_class($instance);
		else
			$class   = $instance->getName();

		//Prepare the context
		$status = new Aspect_Meta_Status();
		$meta   = new Aspect_Meta($class, $func, $instance, $status, $args);

		//Pre-pend context to args
		array_unshift($args, $meta);

		//Call the Before chain
		self::execChain(self::CHAIN_PRE1, false,
					$class, $func, $args, $instance);

		//Call the AroundBefore
		$return = self::execChain(self::CHAIN_PRE2, true,
						$class, $func, $args, $instance);

		//Check for stop/replace
		$stop = self::stopAndMerge($return, $args);

		//A stop was not returned, do not skip call and AroundAfter
		if(!$stop)
		{
			//Remove the meta object for the actual method call
			$meta = array_shift($args);

			//Need for specific functionality of Wrap
			if($func != 'wrapInstance')
				$r = call_user_func_array(array($instance, $func), $args);
			else
				$r = Aspect_Wrapper::wrap($instance, $args);

			//Set return to function call
			$status->setReturn($r);

			//Add Context information
			array_unshift($args, $meta);

			//Execute AroundAfter
			$return = self::execChain(self::CHAIN_POST1, true,
							$class, $func, $args, $instance);

			//Check for Stop/Replace
			$stop = self::stopAndMerge($return, $args);

			//Check if a stop was done to replace the return value and
			//set aborted flag
			if($stop)
			{
				$status->setReturn($result->stopValue());
				$status->aborted();
			}
		}
		else
		{
			//Requested to skip call from AroundBefore
			//Set aborted and return value
			$status->setReturn($return->stopValue());
			$status->aborted();
		}

		//Execute After
		self::execChain(self::CHAIN_POST2, false,
					$class, $func, $args, $instance);

		//Return the affected return
		return $args[0]->getReturn();
	}
	/**
	 * Process one location
	 * @param int $chain Location
	 * @param bool $write Write Context flag
	 * @param string $class Calling Classname
	 * @param string $func Calling Methodname
	 * @param array $args Arguments passed : 0 might be a meta
	 * @param mixed $instance Object
	 * @return mixed Return value
	 */
	private static function execChain($chain, $write,
			$class, $func, &$args, $instance)
	{
		$return  = null;
		//Process function keys first (Simple Match)
		if(isset(self::$_perfunc[$func]))
			foreach(self::$_perfunc[$func][$chain] as $aspect)
			{
				//Advice matches?
				if($aspect->canFire($class, $func,$instance))
				{
					//Execute the bind point
					$return = $aspect->fire($class, $func, $args, $instance);
					//If read/write
					if($write)
					{
						$stop = self::stopAndMerge($return, $args);
						if($stop) return $return;
					}
				}
			}

		//Process classes keys second (Simple Match)
		if(isset(self::$_perclass[$class]))
			foreach(self::$_perclass[$class][$chain] as $aspect)
			{
				if($aspect->canFire($class, $func, $instance))
				{
					$return = $aspect->fire($class, $func, $args, $instance);
					if($write)
					{
						$stop = self::stopAndMerge($return, $args);
						if($stop) return $return;
					}
				}
			}
			
		//Process Globals
		foreach(self::$_global[$chain] as $aspect)
			if($aspect->canFire($class, $func, $instance))
			{
				$return = $aspect->fire($class, $func, $args, $instance);
				if($write)
				{
					$stop = self::stopAndMerge($return, $args);
					if($stop) return $return;
				}
			}
		return $return;
	}

	//Attempt at serialization, needs cleaning
	public function __sleep()
	{
		$this->temp = array(
			self::$_global,
			self::$_perclass,
			self::$_perfunc
		);
		return array('temp');
	}
	public function __wakeup()
	{
		self::$_global      = $this->temp[0];
		self::$_perclass    = $this->temp[1];
		self::$_perfunc     = $this->temp[2];
		unset($this->temp);
	}
}
