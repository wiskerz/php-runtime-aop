<?php
class MetaStatusTest extends PHPUnit_Framework_TestCase
{
	public static function dataPool()
	{
		return array(
			array("test"),
			array(null),
			array(new stdClass()),
			array(3),
			array(2.13),
			array(-24)
		);
	}

	public function testDefaults()
	{
		$status = new Aspect_Meta_Status();

		$this->assertFalse($status->aborted());
		$this->assertEquals(null, $status->getReturn());
	}
	/**
	 * @depends testDefaults
	 */
	public function testAbort()
	{
		$status = new Aspect_Meta_Status();

		$status->setAbort();
		$this->assertTrue($status->aborted());
		$status->clearAbort();
		$this->assertFalse($status->aborted());
	}
	/**
	 * @depends testDefaults
	 * @dataProvider dataPool
	 */
	public function testAbortValue($ret)
	{
		$status = new Aspect_Meta_Status();

		$status->setReturn($ret);
		$this->assertEquals($ret, $status->getReturn());
	}
}
