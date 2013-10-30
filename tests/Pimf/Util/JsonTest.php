<?php
class JsonTest extends PHPUnit_Framework_TestCase
{
  public function testEncodingAndDecodingValidData()
  {
    # test 1
    $item1 = array(1,2,3);
    $encode = \Pimf\Util\Json::encode($item1);

    $this->assertSame($item1, \Pimf\Util\Json::decode($encode));
  }

  public function testHandlingWithObjects()
  {
    $jsonModel = new \ArrayObject(array(
      'title'   => 'my title',
      'content' => 'my content',
      'id'      => 1
    ));

    $encode = \Pimf\Util\Json::encode($jsonModel->getArrayCopy());
    $decode = \Pimf\Util\Json::decode($encode);

    $this->assertObjectHasAttribute('title', $decode);
    $this->assertObjectHasAttribute('content', $decode);
    $this->assertObjectHasAttribute('id', $decode);

    $this->assertEquals($decode->title, 'my title');
    $this->assertEquals($decode->content, 'my content');
  }

  /**
   * @expectedException RuntimeException
   */
  public function testMalformedJSON()
  {
    \Pimf\Util\Json::decode("{'title': 'my second title'}");
  }
}
