<?php

namespace DelOlmo\Token;

/**
 * An interface for an expirable token object.
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
interface ExpirableTokenInterface extends TokenInterface
{
    /**
     * Returns the date and time on which the token expires
     *
     * @return \DateTime
     */
    public function getExpiresAt(): \DateTime;

    /**
     * Whether or not the current token is expired.
     *
     * @return bool
     */
    public function isExpired(): bool;
}
