<?php

/**
 * Class ReflectMe is a fixture
 */
class ReflectMe
{
    protected $id, $name;

    public function __get($prop)
    {
        return $this->$prop;
    }
}

class DataMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Pimf\DataMapper\Base
     */
    protected function getDataMapper()
    {
        return $this->getMockForAbstractClass(
            '\\Pimf\\DataMapper\\Base', array(), '', false
        );
    }


    # start testing


    public function testHappyReflecting()
    {
        $mock = $this->getDataMapper();
        $model = new \ReflectMe();
        $reflected = $mock->reflect($model, 123, 'id');

        $this->assertEquals($reflected->id, $model->id);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testHappyReflectingUndefinedProperty()
    {
        $mock = $this->getDataMapper();
        $model = new \ReflectMe();

        $mock->reflect($model, 123, 'undefined-prop');
    }
}
 