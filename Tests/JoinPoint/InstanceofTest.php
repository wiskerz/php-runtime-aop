<?php
interface intTest{}
class test {}
class sub extends test{}
class other implements intTest{}

class InstanceofTest extends PHPUnit_Framework_TestCase
{

	public function testDirectClass()
	{
		$jp = new Aspect_JoinPoint_Instanceof("test");
		$x	= new test();

		$this->assertTrue($jp->test("test", "func", $x));
	}
	public function testChildClass()
	{
		$jp = new Aspect_JoinPoint_Instanceof("test");
		$x	= new sub();

		$this->assertTrue($jp->test("sub", "func", $x));		
	}
	public function testInterface()
	{
		$jp = new Aspect_JoinPoint_Instanceof("intTest");
		$x	= new other();

		$this->assertTrue($jp->test("other", "func", $x));		
	}
	public function testFail()
	{
		$jp = new Aspect_JoinPoint_Instanceof("intTest");

		$this->assertFalse($jp->test("other", "func", null));		
	}

}
