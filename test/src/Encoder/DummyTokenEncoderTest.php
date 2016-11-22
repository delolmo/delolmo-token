<?php

namespace DelOlmo\Token\Encoder;

/**
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class DummyTokenEncoderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \DelOlmo\Token\Encoder\DummyTokenEncoder
     */
    protected $encoder;

    protected function setUp()
    {
        $this->encoder = new DummyTokenEncoder();
    }

    protected function tearDown()
    {
        $this->encoder = null;
    }

    public function testHashAndValidation()
    {
        $hash = $this->encoder->hash('foo');
        $this->assertTrue($this->encoder->verify('foo', $hash));
        $this->assertTrue($this->encoder->verify($hash, 'foo'));
    }

}
