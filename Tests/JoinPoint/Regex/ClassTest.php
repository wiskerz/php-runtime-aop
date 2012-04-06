<?php
class RegexClassTest extends PHPUnit_Framework_TestCase
{
	public static function objectPool()
	{
		return array(
			array("Joker", "/^.*$/"),
			array("MysqlAdapter", "/^[a-zA-Z]+Adapter$/"),
			array("DbFactory", "/^.*Fac.*$/")
		);
	}
	public static function wrongObjectPool()
	{
		return array(
			array("Joker", "/^[a-zA-Z]+Adapter$/"),
			array("Database", "/^.*Fac.*$/")
		);
	}
	/**
	 * @dataProvider objectPool
	 */
	public function testMatch($class, $pattern)
	{
		$jp = new Aspect_JoinPoint_Regex_Class($pattern);
		$this->assertTrue($jp->test($class, "test", null));
	}

	/**
	 * @dataProvider wrongObjectPool
	 */
	public function testMismatch($class, $pattern)
	{
		$jp = new Aspect_JoinPoint_Regex_Class($pattern);
		$this->assertFalse($jp->test($class, "test", null));	
	}

}
