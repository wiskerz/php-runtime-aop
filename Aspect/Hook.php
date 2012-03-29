<?php
class Aspect_Hook
{
	private $_sequence	 = array();
	private $_classes	 = array();
	private $_functions  = array();

	public function __construct($sequence = array(), $class = array() ,
			$function = array())
	{
		$this->_sequence	= $sequence;
		$this->_classes		= $class;
		$this->_functions	= $function;
	}
	public function getClasses()	{ return $this->_classes;	}
	public function getFunctions()	{ return $this->_functions;	}
	public function getSequence()	{ return $this->_sequence;	}
}