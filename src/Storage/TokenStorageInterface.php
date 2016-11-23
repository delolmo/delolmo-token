<?php

namespace DelOlmo\Token\Storage;

use DelOlmo\Token\Storage\BaseTokenStorageInterface as BaseInterface;

/**
 * Stores token objects implementing TokenInterface.
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
interface TokenStorageInterface extends BaseInterface
{

    /**
     * Stores a token.
     *
     * @param string $tokenId The token id
     * @param string $value   The hashed token value
     * @return void
     */
    public function setToken(string $tokenId, string $value);
}
