<?php

namespace DelOlmo\Token;

use DelOlmo\Token\DeactivableToken;

/**
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class DeactivableTokenTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \DelOlmo\Token\DeactivableToken
     */
    protected $token1;

    /**
     * @var \DelOlmo\Token\DeactivableToken
     */
    protected $token2;

    protected function setUp()
    {
        $this->token1 = new DeactivableToken('foo', 'bar', new \DateTime(), true);
        $this->token2 = new DeactivableToken('foo', 'bar', new \DateTime(), false);
    }

    protected function tearDown()
    {
        $this->token1 = null;
        $this->token2 = null;
    }

    /**
     * @covers DelOlmo\Token\DeactivableToken::isActive
     */
    public function testIsActive()
    {
        $this->assertTrue($this->token1->isActive());
        $this->assertFalse($this->token2->isActive());
    }

}
