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
     * @return void
     */
    public function setToken(string $tokenId, string $value, \DateTime $expiresAt);
    
    /**
     * Expires the latest valid token.
     * 
     * @param string $tokenId
     * @return string|null Returns the expired hashed token value if a valid
     * one existed, NULL otherwise
     */
    public function expireToken(string $tokenId);
    
}
