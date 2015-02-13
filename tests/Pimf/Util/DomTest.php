<?php
class DomTest extends \PHPUnit_Framework_TestCase
{
  /**
   * A test-HTML content
   * @var string
   */
  protected static $html;

  public static function setUpBeforeClass()
  {
    self::$html = file_get_contents(
      dirname(__FILE__).'/_fixture/samp-html-with-js-and-css-markup.html'
    );
  }


  # start testing


  public function testCreatingNewInstance()
  {
    new \Pimf\Util\Dom();
  }

  public function testFetchingAllJsResources()
  {
    $dom = new \Pimf\Util\Dom();
    $dom->loadHTML(self::$html);

    $res = $dom->getScriptURLs();

    $this->assertInternalType('array', $res);
    $this->assertNotEmpty($res);

    $this->assertEquals(

      array(
        'http://test.de/templates/522_image.js',
        'http://test.de/templates/522_image2.js',
        'http://test.de/templates/522_image3.js',
      ),

      $res,

      'not expected list of src-values inside of a script-tag'

    );
  }

  public function testFetchingAllCssResources()
  {
    $dom = new \Pimf\Util\Dom();
    $dom->loadHTML(self::$html);

    $res = $dom->getCssURLs();

    $this->assertInternalType('array', $res);
    $this->assertNotEmpty($res);

    $this->assertEquals(

      array(
        'http://test.de/css/layout.css',
        'http://test.de/css/layout2.css',
        'http://test.de/css/layout3.css',
      ),

      $res,

      'not expected list of href-values inside of a link-tag'

    );
  }

  public function testFetchingAllLinkResources()
  {
    $dom = new \Pimf\Util\Dom();
    $dom->loadHTML(self::$html);

    $res = $dom->getURLs();

    $this->assertInternalType('array', $res);
    $this->assertNotEmpty($res);

    $this->assertEquals(

      array(
        'http://test1.de',
        'http://test2.de',
      ),

      $res,

      'not expected list of href-values inside of a a-tag'

    );
  }


  public function testFetchingAllImgResources()
  {
    $dom = new \Pimf\Util\Dom();
    $dom->loadHTML(self::$html);

    $res = $dom->getImageURLs();

    $this->assertInternalType('array', $res);
    $this->assertNotEmpty($res);

    $this->assertEquals(

      array(
        'http://test1.de/image1.jpg',
        'http://test2.de/image2.jpg',
      ),

      $res,

      'not expected list of href-values inside of a a-tag'

    );
  }

}
 