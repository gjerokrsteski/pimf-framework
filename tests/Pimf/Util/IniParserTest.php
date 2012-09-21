<?php
class FrameWorkConfigLoaderTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new Pimf_Util_IniParser(dirname(__FILE__) . '/_fixture/test-config.ini');
  }

  public function testRead()
  {
    $parser = new Pimf_Util_IniParser(dirname(__FILE__) . '/_fixture/test-config.ini');
    $config = $parser->parse();

    $this->assertObjectHasAttribute('environment', $config);
    $this->assertEquals('testing', $config->environment);

    $this->assertObjectHasAttribute('testing', $config);
    $this->assertObjectHasAttribute('staging', $config);
    $this->assertObjectHasAttribute('production', $config);

    $confTesting = $config['testing'];
    $confStaging = $config['staging'];
    $confProd    = $config['production'];

    $this->assertInternalType('array', $confTesting->secrets);
    $this->assertEquals(array(1,2,3), $confTesting->secrets);

    $this->assertEquals('', $confTesting->database->username);
    $this->assertEquals('staging', $confStaging->database->username);
    $this->assertEquals('root', $confProd->database->username);

    $this->assertEmpty($confTesting->database->password);
    $this->assertEquals($confStaging->database->password, $confProd->database->password);

    $this->assertEquals('1', $confTesting->debug);
    $this->assertEquals('1', $confStaging->debug);
    $this->assertEquals('', $confProd->debug);
  }
}
