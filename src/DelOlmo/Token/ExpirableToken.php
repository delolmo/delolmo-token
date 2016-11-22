<?php

namespace DelOlmo\Token;

/**
 * An expirable token object.
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class ExpirableToken extends Token implements ExpirableTokenInterface
{

    /**
     * @var \DateTime
     */
    protected $expires_at = null;

    /**
     * Constructor
     *
     * @param string $id
     * @param string $value
     * @param \DateTime $expires_at
     */
    public function __construct(string $id, string $value, \DateTime $expires_at)
    {
        parent::__construct($id, $value);

        $this->expires_at = $expires_at;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresAt(): \DateTime
    {
        return $this->expires_at;
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired(): bool
    {
        $now = new \DateTime('now');

        return $now > $this->expires_at;
    }

}
