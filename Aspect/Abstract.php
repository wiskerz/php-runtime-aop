<?php
abstract class Aspect_Abstract
{

	/**
	 * Aspect Hook Location
	 * @var Aspect_Hook
	 */
	private $_hook;
	private $_id;

	public function __construct(Aspect_Hook $hook)
	{
		$this->setHook($hook);
		$this->_id = uniqid(rand(1000,9999));
	}
	final protected function setHook(Aspect_Hook $hook)
	{
		$this->_hook = $hook;
		
	}
	final public function getAspectId()
	{
		return $this->_id;
	}
	final public function getKeyClasses()
	{
		return $this->_hook->getClasses();
	}
	final public function getKeyFunctions()
	{
		return $this->_hook->getFunctions();
	}
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
    abstract function fire($class, $function, $args, $instance);
}
