<?php
/**
 * @author Tony Hokayem
 */
/**
 * Define the base class for the binding aspect
 * Basic functionality for testing
 * Requires implementation of the fire method
 * @author Tony Hokayem
 * @abstract
 */
abstract class Aspect_Abstract
{
	/**
	* Aspect Hook Location
	* @var Aspect_Hook
	*/
	private $_hook;
	/**
	* Internal Aspect ID used to hook/unhook key
	* @var int
	*/
	private $_id;
	/**
	* @param Aspect_Hook $hook Matching Rules to attach
	*/
	public function __construct(Aspect_Hook $hook)
	{
		$this->setHook($hook);
		$this->_id = uniqid(rand(1000,9999));
	}
	/**
	* For possible extensions give the ability to change the hook
	* @param Aspect_Hook $hook
	* @final
	*/
	final protected function setHook(Aspect_Hook $hook)
	{
		$this->_hook = $hook;
	}
	/**
	* Return the aspect ID (used for Hook/Unhook)
	* @return int
	*/
	final public function getAspectId()
	{
	return $this->_id;
	}
	/**
	* Simple Match Class List
	* @return array Strings of Classes
	*/
	final public function getKeyClasses()
	{
		return $this->_hook->getClasses();
	}
	/**
	* Simple Match Functions List
	* @return array Strings of functions
	*/
	final public function getKeyFunctions()
	{
		return $this->_hook->getFunctions();
	}
	/**
	* Test whether aspect matches location (advice)
	* @param string $class Class Name of calling object
	* @param string $function Method Name called on object
	* @param mixed $instance Instance of object
	* @return bool True to indicate successful match
	*/
	final public function canFire($class, $function, $instance)
	{
		$seq = $this->_hook->getSequence();
		if(is_array($seq[0]))
			$canFire = true;
		else
			$canFire = false;
		foreach($seq as $OrJointPoint)
		{
			if(is_array($OrJointPoint))
			{
				foreach($OrJointPoint as $AndJointPoint)
				{
				$canFire = $canFire
				&& $AndJointPoint->test($class, $function, $instance);
				//Shortcut-And
				if(!$canFire) break;
				}
			}
			else
			{
				$canFire = $canFire
				|| $OrJointPoint->test($class, $function, $instance);
			}
			//Shortcut-Or
			if($canFire) break;
		}
		return $canFire;
	}
	/**
	 * Once the Aspect matches a location with canFire
	 * Provides same information as canFire
	 * $args[0] contains an Aspect_Meta object providing the context
	 * If an Aspect_Result is returned then depending on the location it will
	 * be processed (stop and/or replace)
	 * @see Aspect_Abstract::canFire
	 * @see Aspect_Meta
	 * @see Aspect_Result
	 * @abstract
	 * @return mixed|Aspect_Result
	 */
	abstract function fire($class, $function, $args, $instance);
}
