<?php
class Aspect_Handler
{
    const CHAIN_PRE1    =   1;
    const CHAIN_PRE2    =   2;
    const CHAIN_POST1   =   3;
    const CHAIN_POST2   =   4;

    private static $_global     = array(
            self::CHAIN_PRE1 => array(),
            self::CHAIN_PRE2 => array(),
            self::CHAIN_POST1 => array(),
            self::CHAIN_POST2 => array()
        );
    private static $_perclass   = array(
            self::CHAIN_PRE1 => array(),
            self::CHAIN_PRE2 => array(),
            self::CHAIN_POST1 => array(),
            self::CHAIN_POST2 => array()
        );
    private static $_perfunc    = array(
            self::CHAIN_PRE1 => array(),
            self::CHAIN_PRE2 => array(),
            self::CHAIN_POST1 => array(),
            self::CHAIN_POST2 => array()
        );

    public static function wrapBefore($aspect)
    {
        return self::attachAspect($aspect, self::CHAIN_PRE1, $aspect->getKeyClasses(), $aspect->getKeyFunctions());
    }
    public static function wrapAroundBefore($aspect)
    {
        return self::attachAspect($aspect, self::CHAIN_PRE2,  $aspect->getKeyClasses(), $aspect->getKeyFunctions());
    }
    public static function wrapAroundAfter($aspect)
    {
        return self::attachAspect($aspect, self::CHAIN_POST1,  $aspect->getKeyClasses(), $aspect->getKeyFunctions());
    }
    public static function wrapAfter($aspect)
    {
        return self::attachAspect($aspect, self::CHAIN_POST2,  $aspect->getKeyClasses(), $aspect->getKeyFunctions());
    }

    public static function unwrapBefore($aspect)
    {
        return self::detachAspect($aspect, self::CHAIN_PRE1);
    }
    public static function unwrapAroundBefore($aspect)
    {
        return self::detachAspect($aspect, self::CHAIN_PRE2);
    }
    public static function unwrapAroundAfter($aspect)
    {
        return self::detachAspect($aspect, self::CHAIN_POST1);
    }
    public static function unwrapAfter($aspect)
    {
        return self::detachAspect($aspect, self::CHAIN_POST2);
    }

    
    private static function attachAspect($aspect, $chain, $class = array(), $function = array())
    {

        if(is_array($class))
            foreach($class as $c)
                self::attachAspectToClass($aspect, $chain, $c);
        if(is_array($function))
            foreach($function as $f)
                self::attachAspectToFunction($aspect, $chain, $f);

        if(!$class && !$function)
            self::$_global[$chain][$aspect->getAspectId()] = $aspect;
        
        return true;
    }
	/**
	 * Detatch an Aspect
	 * @param Aspect_Abstract $aspect
	 * @return bool Success
	 */
    private static function detachAspect($aspect, $chain)
    {
        $id			= $aspect->getAspectId ();
		$class		= $aspect->getKeyClasses();
		$function	= $aspect->getKeyFunctions();


        if(is_array($class))
            foreach($class as $c)
                if(isset(self::$_perclass[$c])
                       && isset(self::$_perclass[$c][$chain][$id]))
                   unset(self::$_perclass[$c][$chain][$id]);
                else
                    return false;
                
         if(is_array($function))
            foreach($function as $f)
                if(isset(self::$_perfunc[$f])
                       && isset(self::$_perfunc[$f][$chain][$id]))
                   unset(self::$_perfunc[$f][$chain][$id]);
                else
                    return false;
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
    public static function fire($func, $args, $instance, $isReflection = false)
    {
        if(!$isReflection)
            $class   = get_class($instance);
        else 
            $class   = $instance->getName();

        $status = new Aspect_Meta_Status();
        $meta   = new Aspect_Meta($class, $func, $instance, $status, $args);
        array_unshift($args, $meta);
        
        self::execChain(self::CHAIN_PRE1, false, $class, $func, $args, $instance);
        $return = self::execChain(self::CHAIN_PRE2, true,  $class, $func, $args, $instance);

	$stop = self::stopAndMerge($return, $args);

        $meta = array_shift($args);
        if(!$stop)
        {
            if($func != 'wrapInstance')
                $r      = call_user_func_array(array($instance, $func), $args);
            else
                $r = Aspect_Wrapper::wrap($instance, $args);
            
            $status->setReturn($r);
            array_unshift($args, $meta);
            $return = self::execChain(self::CHAIN_POST1, true, $class, $func, $args, $instance);
            $stop = self::stopAndMerge($return, $args);
            if($stop)
            {
               $status->setReturn($result->stopValue());
               $status->aborted();
            }
        }
        else
        {
            $status->aborted();
            $status->setReturn($return->stopValue());
            array_unshift($args, $meta);
        }
      

        self::execChain(self::CHAIN_POST2, false,  $class, $func, $args, $instance);
        

        return $args[0]->getReturn();

    }
    private static function execChain($chain, $skip, $class, $func, &$args, $instance)
    {
        $return  = null;
        if(isset(self::$_perfunc[$func]))
           foreach(self::$_perfunc[$func][$chain] as $aspect)
            {
                if($aspect->canFire($class, $func,$instance))
                 {
                   $return = $aspect->fire($class, $func, $args, $instance);
                   if($skip)
                   {
                       $stop = self::stopAndMerge($return, $args);
                       if($stop) return $return;
                   }
                }
            }

        if(isset(self::$_perclass[$class]))
           foreach(self::$_perclass[$class][$chain] as $aspect)
           {
                if($aspect->canFire($class, $func, $instance))
                 {
                    $return = $aspect->fire($class, $func, $args, $instance);
                   if($skip)
                   {
                       $stop = self::stopAndMerge($return, $args);
                       if($stop) return $return;
                   }
                }
           }

        foreach(self::$_global[$chain] as $aspect)
            if($aspect->canFire($class, $func, $instance))
            {
                $return = $aspect->fire($class, $func, $args, $instance);
                if($skip)
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
