<?php
class XmlConverterTest extends PHPUnit_Framework_TestCase
{
  public function testConvertStringToSimpleXmlInstance()
  {
    $string = file_get_contents(dirname(__FILE__).'/_fixture/samp.xml');
    $simpleXml = Pimf_Util_Xml::toSimpleXMLElement($string);

    $this->assertInstanceOf('SimpleXMLElement', $simpleXml);
  }

  public function testConvertFileToSimpleXmlInstance()
  {
    $file = dirname(__FILE__).'/_fixture/samp.xml';

    $simpleXml = Pimf_Util_Xml::toSimpleXMLElement($file);

    $this->assertInstanceOf('SimpleXMLElement', $simpleXml);
  }

  public function testConvertStringToDOMDocumentInstance()
  {
    $string = file_get_contents(dirname(__FILE__).'/_fixture/samp.xml');

    $dom = Pimf_Util_Xml::toDOMDocument($string);

    $this->assertInstanceOf('DOMDocument', $dom);
  }

  public function testConvertFileToDOMDocumentInstance()
  {
    $file = dirname(__FILE__).'/_fixture/samp.xml';

    $dom = Pimf_Util_Xml::toDOMDocument($file);

    $this->assertInstanceOf('DOMDocument', $dom);
  }

  public function testConvertSimpleXmlInstanceToArray()
  {
    $file = dirname(__FILE__).'/_fixture/samp.xml';

    $simpleXml = Pimf_Util_Xml::toSimpleXMLElement($file);
    $result    = Pimf_Util_Xml::toArray($simpleXml);

    $this->assertInternalType('array', $result);
    $this->assertNotEmpty($result);
    $this->assertArrayHasKey('data', $result);
    $this->assertEquals(2, count($result['data']['row']));
  }

  public function testConvertSimleXmlToArrayUsingNamespace()
  {
    $file = dirname(__FILE__).'/_fixture/samp-with-namespace.xml';

    $simpleXml = Pimf_Util_Xml::toSimpleXMLElement($file);
    $result    = Pimf_Util_Xml::toArray($simpleXml, 'pimf');

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

    $simpleXml = Pimf_Util_Xml::toSimpleXMLElement($file);
    Pimf_Util_Xml::toArray($simpleXml, 'some-bad-namespace');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testConvertToSimpleXmlThrowingException()
  {
    Pimf_Util_Xml::toSimpleXMLElement(new stdClass());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testConvertToDOMDocThrowingException()
  {
    Pimf_Util_Xml::toDOMDocument(new stdClass());
  }
}