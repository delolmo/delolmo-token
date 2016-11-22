<?php

namespace DelOlmo\Token\Storage;

use DelOlmo\Token\Storage\BaseTokenStorageInterface as BaseInterface;

/**
 * Stores token objects implementing DeactivableTokenInterface.
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
interface DeactivableTokenStorageInterface extends BaseInterface
{

    /**
     * Stores a token.
     *
     * @param string $tokenId The token id
     * @param string $value The hashed token value
     * @param \DateTime $expiresAt The date and time on which the token expires
     * @param bool $active Whether the token must be stored as active or not
     */
    public function setToken(string $tokenId, string $value, \DateTime $expiresAt, bool $active);
}
