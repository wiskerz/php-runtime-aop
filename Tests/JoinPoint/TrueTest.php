<?php
class TrueTest extends PHPUnit_Framework_TestCase
{
	private $_joinPoint;
	protected function setUp()
	{
		$this->_joinPoint = new Aspect_JoinPoint_True();
	}
	public function testMatch()
	{
		$this->assertTrue($this->_joinPoint->test("test", "test", null));
	}

}
