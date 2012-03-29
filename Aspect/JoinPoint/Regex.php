<?php
abstract class Aspect_JoinPoint_Regex
    extends Aspect_JoinPoint_Abstract
{

        private $_regex;
	public function __construct($regex)
        {
            $this->_regex = $regex;
        }
        protected function _matchRegex($pattern, $text)
        {
            return preg_match($pattern, $text);
        }
        protected function _testRegex($text)
        {
            return preg_match($this->_regex, $text);
        }
}
