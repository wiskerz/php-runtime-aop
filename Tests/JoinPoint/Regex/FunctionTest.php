<?php
class RegexFunctionTest extends PHPUnit_Framework_TestCase
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
	public function testMatch($func, $pattern)
	{
		$jp = new Aspect_JoinPoint_Regex_Function($pattern);
		$this->assertTrue($jp->test("test", $func, null));
	}

	/**
	 * @dataProvider wrongObjectPool
	 */
	public function testMismatch($func, $pattern)
	{
		$jp = new Aspect_JoinPoint_Regex_Function($pattern);
		$this->assertFalse($jp->test("test", $func, null));
	}

}
