<?php

namespace DelOlmo\Token;

/**
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class ExpirableTokenTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \DelOlmo\Token\ExpirableToken
     */
    protected $token1;

    /**
     * @var \DelOlmo\Token\ExpirableToken
     */
    protected $token2;

    protected function setUp()
    {
        $datetime1 = new \DateTime('1987/05/14 00:00:00');
        $this->token1 = new ExpirableToken('foo', 'bar', $datetime1);

        $datetime2 = new \DateTime('2087/05/14 00:00:00');
        $this->token2 = new ExpirableToken('foo', 'bar', $datetime2);
    }

    protected function tearDown()
    {
        $this->token1 = null;
        $this->token2 = null;
    }

    /**
     * @covers DelOlmo\Token\ExpirableToken::getExpiresAt
     */
    public function testGetExpiresAt()
    {
        $datetime1 = new \DateTime('1987/05/14 00:00:00');
        $this->assertEquals($this->token1->getExpiresAt(), $datetime1);

        $datetime2 = new \DateTime('2087/05/14 00:00:00');
        $this->assertEquals($this->token2->getExpiresAt(), $datetime2);
    }

    /**
     * @covers DelOlmo\Token\ExpirableToken::isExpired
     */
    public function testIsExpired()
    {
        $this->assertTrue($this->token1->isExpired());
        $this->assertFalse($this->token2->isExpired());
    }

}
