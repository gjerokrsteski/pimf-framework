<?php
class XmlConverterTest extends PHPUnit_Framework_TestCase
{
  public function testConvertStringToSimpleXmlInstance()
  {
    $string = file_get_contents(dirname(__FILE__).'/_fixture/samp.xml');
    $simpleXml = \Pimf\Util\Xml::toSimpleXMLElement($string);

    $this->assertInstanceOf('SimpleXMLElement', $simpleXml);
  }

  public function testConvertFileToSimpleXmlInstance()
  {
    $file = dirname(__FILE__).'/_fixture/samp.xml';

    $simpleXml = \Pimf\Util\Xml::toSimpleXMLElement($file);

    $this->assertInstanceOf('SimpleXMLElement', $simpleXml);
  }

  public function testConvertStringToDOMDocumentInstance()
  {
    $string = file_get_contents(dirname(__FILE__).'/_fixture/samp.xml');

    $dom = \Pimf\Util\Xml::toDOMDocument($string);

    $this->assertInstanceOf('DOMDocument', $dom);
  }

  public function testConvertFileToDOMDocumentInstance()
  {
    $file = dirname(__FILE__).'/_fixture/samp.xml';

    $dom = \Pimf\Util\Xml::toDOMDocument($file);

    $this->assertInstanceOf('DOMDocument', $dom);
  }

  public function testConvertSimpleXmlInstanceToArray()
  {
    $file = dirname(__FILE__).'/_fixture/samp.xml';

    $simpleXml = \Pimf\Util\Xml::toSimpleXMLElement($file);
    $result    = \Pimf\Util\Xml::toArray($simpleXml);

    $this->assertInternalType('array', $result);
    $this->assertNotEmpty($result);
    $this->assertArrayHasKey('data', $result);
    $this->assertEquals(2, count($result['data']['row']));
  }

  public function testConvertSimleXmlToArrayUsingNamespace()
  {
    $file = dirname(__FILE__).'/_fixture/samp-with-namespace.xml';

    $simpleXml = \Pimf\Util\Xml::toSimpleXMLElement($file);
    $result    = \Pimf\Util\Xml::toArray($simpleXml, 'pimf');

    $this->assertNotEmpty($result);
    $this->assertArrayHasKey('MediaElementGroup', $result);
    $this->assertArrayHasKey('ScaleCollection', $result);
  }

  /**
   * @expectedException OutOfBoundsException
   */
  public function testConvertSimleXmlToArrayUsingNotDefinedNamespace()
  {
    $file = dirname(__FILE__).'/_fixture/samp-with-namespace.xml';

    $simpleXml = \Pimf\Util\Xml::toSimpleXMLElement($file);
    \Pimf\Util\Xml::toArray($simpleXml, 'some-bad-namespace');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testConvertToSimpleXmlThrowingException()
  {
    \Pimf\Util\Xml::toSimpleXMLElement(new stdClass());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testConvertToDOMDocThrowingException()
  {
    \Pimf\Util\Xml::toDOMDocument(new stdClass());
  }
}