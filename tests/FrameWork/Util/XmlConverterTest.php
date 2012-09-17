<?php
class XmlConverterTest extends PHPUnit_Framework_TestCase
{
  public function testConvertStringToSimpleXmlInstance()
  {
    $string = file_get_contents(dirname(__FILE__).'/_fixture/samp.xml');

    $xml       = new Pimf_Util_Xml();
    $simpleXml = $xml->toSimpleXMLElement($string);

    $this->assertInstanceOf('SimpleXMLElement', $simpleXml);
  }

  public function testConvertFileToSimpleXmlInstance()
  {
    $file = dirname(__FILE__).'/_fixture/samp.xml';

    $xml       = new Pimf_Util_Xml();
    $simpleXml = $xml->toSimpleXMLElement($file);

    $this->assertInstanceOf('SimpleXMLElement', $simpleXml);
  }

  public function testConvertStringToDOMDocumentInstance()
  {
    $string = file_get_contents(dirname(__FILE__).'/_fixture/samp.xml');

    $xml = new Pimf_Util_Xml();
    $dom = $xml->toDOMDocument($string);

    $this->assertInstanceOf('DOMDocument', $dom);
  }

  public function testConvertFileToDOMDocumentInstance()
  {
    $file = dirname(__FILE__).'/_fixture/samp.xml';

    $xml = new Pimf_Util_Xml();
    $dom = $xml->toDOMDocument($file);

    $this->assertInstanceOf('DOMDocument', $dom);
  }

  public function testConvertSimpleXmlInstanceToArray()
  {
    $file = dirname(__FILE__).'/_fixture/samp.xml';

    $utilXml   = new Pimf_Util_Xml();
    $simpleXml = $utilXml->toSimpleXMLElement($file);
    $result    = $utilXml->toArray($simpleXml);

    $this->assertInternalType('array', $result);
    $this->assertNotEmpty($result);
    $this->assertArrayHasKey('data', $result);
    $this->assertEquals(2, count($result['data']['row']));
  }

  public function testConvertSimleXmlToArrayUsingNamespace()
  {
    $file = dirname(__FILE__).'/_fixture/samp-with-namespace.xml';

    $utilXml   = new Pimf_Util_Xml();
    $simpleXml = $utilXml->toSimpleXMLElement($file);
    $result    = $utilXml->toArray($simpleXml, 'efs');

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

    $utilXml   = new Pimf_Util_Xml();
    $simpleXml = $utilXml->toSimpleXMLElement($file);
    $result    = $utilXml->toArray($simpleXml, 'some-bad-namespace');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testConvertToSimpleXmlThrowingException()
  {
    $utilXml   = new Pimf_Util_Xml();
    $simpleXml = $utilXml->toSimpleXMLElement(new stdClass());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testConvertToDOMDocThrowingException()
  {
    $utilXml   = new Pimf_Util_Xml();
    $simpleXml = $utilXml->toDOMDocument(new stdClass());
  }
}