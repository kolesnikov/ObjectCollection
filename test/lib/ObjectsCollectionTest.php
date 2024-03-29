<?php

require_once dirname(__FILE__) . '/../../lib/objectscollection.php';

/**
 * Test class for ObjectsCollection.
 * Generated by PHPUnit on 2012-04-24 at 17:07:52.
 */
class ObjectsCollectionTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ObjectsCollection
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new ObjectsCollection('ObjectTest');
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		
	}

	public function testConctructorException()
	{
		try
		{
			$object	= new ObjectsCollection('ObjectException');
		}
		catch (Exception $e){}
		
		if ( !(isset($e) && $e instanceof InvalidArgumentException) )
		{
			$this->fail('Конструктор не должен принимать классы которых нет');
		}
	}
	/**
	 * @covers ObjectsCollection::push
	 * @todo Implement testPush().
	 */
//	public function testPush() {
//		// Remove the following lines when you implement this test.
//		$this->markTestIncomplete(
//				'This test has not been implemented yet.'
//		);
//	}

}

Class ObjectTest
{
	
}

?>
