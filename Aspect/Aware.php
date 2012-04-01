<?php
/**
 * @author Tony Hokayem
 */
/**
 * Interface to indicate that the object knows that it is used in the
 * context of AOP. Will provide him with additional functionality
 * @author Tony Hokayem
 */
interface Aspect_Aware
{
	/**
	 * @param Aspect_Wrapper Final Wrapped version of the object
	 */
	public function __Aware_setWrapper($w);
}
