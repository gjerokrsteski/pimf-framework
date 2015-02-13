<?php
class TestAccumulatorClass
{
  /**
   * @var array
   */
  protected $baterry;

  public function __construct(array $items = array())
  {
    $this->baterry = $items;
  }

  public function getAll()
  {
    return $this->baterry;
  }
}

class SerializerTest extends \PHPUnit_Framework_TestCase
{
  /**
   * Provides test data.
   * @return array
   */
  public function objectsProvider()
  {
    $stdClass        = new \stdClass();
    $stdClass->title = 'Zweiundvierz';
    $stdClass->from  = 'Joe';
    $stdClass->to    = 'Jane';
    $stdClass->body  = 'Ich kenne die Antwort -- aber was ist die Frage?';

    return array(
      array( $stdClass ),
      array( new \TestAccumulatorClass() ),
      array( 'i am a string' ),
      array( '123456789' ),
      array(
        array(
          '1',
          '2',
          '3',
          '4' => '5',
          '6' => '7',
          'item1' => new \TestAccumulatorClass(),
          'item2' => $stdClass,
        )
      )
    );
  }

  /**
   * @param mixed $object
   * @dataProvider objectsProvider
   */
  public function testSerializingSomeObjects($object)
  {
    \Pimf\Util\Serializer::serialize($object);
  }

  /**
   * @param mixed $item
   * @dataProvider objectsProvider
   */
  public function testUnserializingSomeObjectsAndCompareEachother($item)
  {
    $serializedItem   = \Pimf\Util\Serializer::serialize($item);
    $unserializedItem = \Pimf\Util\Serializer::unserialize($serializedItem);

    $this->assertEquals(gettype($item), gettype($unserializedItem));
  }

  public function testHandlingWithBigBigClassObject()
  {
    $bigData = array_fill(0, 100, array_fill(1, 6, new TestAccumulatorClass()));
    $testObject = new \TestAccumulatorClass($bigData);

    $serializedData   = \Pimf\Util\Serializer::serialize($testObject);
    $unserializedData = \Pimf\Util\Serializer::unserialize($serializedData);

    $this->assertEquals(gettype($testObject), gettype($unserializedData));
    $this->assertObjectHasAttribute('baterry', $unserializedData);
    $this->assertNotEmpty($unserializedData->getAll());
    $this->assertEquals(100, count($unserializedData->getAll()));
   }

  public function testHandlingWithBigBigArrayData()
  {
    $bigData = array_fill(0, 100, array_fill(1, 6, 'banana'));

    $serializedData   = \Pimf\Util\Serializer::serialize($bigData);
    $unserializedData = \Pimf\Util\Serializer::unserialize($serializedData);

    $this->assertEquals(gettype($bigData), gettype($unserializedData));
  }

  public function testHandlingWithBigBigArrayOfObjectsData()
  {
    $stdClass        = new \stdClass();
    $stdClass->title = 'Zweiundvierzig';
    $stdClass->from  = 'Joe';
    $stdClass->to    = 'Jane';
    $stdClass->body  = 'Ich kenne die Antwort?';

    $bigData = array_fill(0, 100, array_fill(1, 4, $stdClass));

    $serializedData   = \Pimf\Util\Serializer::serialize($bigData);
    $unserializedData = \Pimf\Util\Serializer::unserialize($serializedData);

    $this->assertEquals(gettype($bigData), gettype($unserializedData));
  }

  public function testHandlingWithBigBigObjectOfArraysData()
  {
    $stdClass        = new \stdClass();
    $bigData         = array_fill(0, 500, array_fill(1, 4, 'std trash'));
    $stdClass->array = $bigData;

    $serializedData   = \Pimf\Util\Serializer::serialize($stdClass);
    $unserializedData = \Pimf\Util\Serializer::unserialize($serializedData);

    $this->assertEquals(gettype($stdClass), gettype($unserializedData));
  }

  public function testSimpleXMLElement()
  {
    $xml = new \SimpleXMLElement('<example><test/></example>');
    $serializedData   = \Pimf\Util\Serializer::serialize($xml);
    $unserializedData = \Pimf\Util\Serializer::unserialize($serializedData);
    $this->assertEquals(gettype($xml), gettype($unserializedData));
  }

  /**
   * @expectedException \PHPUnit_Framework_Error_Notice
   */
  public function testBombingExceptionOnUnserialize()
  {
    \Pimf\Util\Serializer::unserialize(null);
  }
}
