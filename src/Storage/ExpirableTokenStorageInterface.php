<?php

namespace DelOlmo\Token\Storage;

use DelOlmo\Token\Storage\BaseTokenStorageInterface as BaseInterface;

/**
 * Stores token objects implementing ExpirableTokenInterface.
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
interface ExpirableTokenStorageInterface extends BaseInterface
{

    /**
     * Stores a token.
     *
     * @param string $tokenId The token id
     * @param string $value The hashed token value
     * @param \DateTime $expiresAt The date and time on which the token expires
     */
    public function setToken(string $tokenId, string $value, \DateTime $expiresAt);
}
