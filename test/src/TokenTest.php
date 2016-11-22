<?php

namespace DelOlmo\Token;

/**
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class TokenTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \DelOlmo\Token\Token
     */
    protected $token;

    protected function setUp()
    {
        $this->token = new Token('foo', 'bar');
    }

    protected function tearDown()
    {
        $this->token = null;
    }

    /**
     * @covers DelOlmo\Token\Token::__toString
     */
    public function test__toString()
    {
        $this->assertSame((string) $this->token, 'bar');
    }

    /**
     * @covers DelOlmo\Token\Token::getId
     */
    public function testGetId()
    {
        $this->assertSame($this->token->getId(), 'foo');
    }

    /**
     * @covers DelOlmo\Token\Token::getValue
     */
    public function testGetValue()
    {
        $this->assertSame($this->token->getValue(), 'bar');
    }

}
