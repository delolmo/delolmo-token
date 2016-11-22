<?php

namespace DelOlmo\Token;

/**
 * A deactivable token object.
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class DeactivableToken extends ExpirableToken implements DeactivableTokenInterface
{

    /**
     * @var bool
     */
    protected $active;

    /**
     * Constructor
     *
     * @param string $id
     * @param string $value
     * @param \DateTime $expires_at
     * @param bool $active
     */
    public function __construct(string $id, string $value, \DateTime $expires_at, bool $active = true)
    {
        parent::__construct($id, $value, $expires_at);
        $this->active = $active;
    }

    /**
     * Whether the Token is still active or not
     *
     * Inactive tokens are those that have been flagged as inactive
     *
     * @return type
     */
    public function isActive(): bool
    {
        return $this->active;
    }
}
