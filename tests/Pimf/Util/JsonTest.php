<?php
class TestJsonModel extends Pimf_Model_ToArray
{
  private $id = 1;

  protected $title = 'my title';

  public $content = 'my content';
}


class JsonTest extends PHPUnit_Framework_TestCase
{
  public function testEncodingAndDecodingValidData()
  {
    # test 1
    $item1 = array(1,2,3);
    $encode = Pimf_Util_Json::encode($item1);

    $this->assertSame($item1, Pimf_Util_Json::decode($encode));
  }

  public function testHandlingWithObjects()
  {
    $jsonModel = new TestJsonModel();
    $encode = Pimf_Util_Json::encode($jsonModel->toArray());
    $decode = Pimf_Util_Json::decode($encode);

    $this->assertObjectHasAttribute('title', $decode);
    $this->assertObjectHasAttribute('content', $decode);
    $this->assertObjectNotHasAttribute('id', $decode);

    $this->assertEquals($decode->title, 'my title');
    $this->assertEquals($decode->content, 'my content');
  }

  /**
   * @expectedException RuntimeException
   */
  public function testMalformedJSON()
  {
    Pimf_Util_Json::decode("{'title': 'my second title'}");
  }
}
