<?php

namespace DelOlmo\Token\Encoder;

/**
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class NativePasswordTokenEncoderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var NativePasswordTokenEncoder
     */
    protected $encoder;

    protected function setUp()
    {
        $this->encoder = new NativePasswordTokenEncoder();
    }

    protected function tearDown()
    {
        $this->encoder = null;
    }

    public function testHashAndValidation()
    {
        $hash = $this->encoder->hash('foo');
        $this->assertTrue($this->encoder->verify('foo', $hash));
    }

}
