<?php

namespace DelOlmo\Token\Storage;

/**
 * A basic interface for a token storage.
 *
 * It is up to the specific implementation of the BaseTokenStorageInterface
 * object to:
 * - Implement a method to store tokens.
 * - Decide whether or not a token is valid, even if it exists in storage.
 *
 * Objects implementing BaseTokenStorageInterface should be unaware of any
 * encoding/hashing method used to store/read tokens. TokenStorage objects
 * should only work with values that have already been hashed/encoded.
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
interface BaseTokenStorageInterface
{
    /**
     * Returns the stored value of a valid token.
     *
     * @param string $tokenId The token id
     * @return string The stored hashed value of the token
     * @throws \DelOlmo\Token\Exception\TokenNotFoundException If no valid
     * token exists for given token id
     */
    public function getToken(string $tokenId): string;

    /**
     * Removes a token.
     *
     * @param string $tokenId The token id
     * @return string|null Returns the removed hashed token value if a valid
     * one existed, NULL otherwise
     */
    public function removeToken(string $tokenId);

    /**
     * Checks whether a valid token exists for the given token id.
     *
     * @param string $tokenId The token id
     * @return bool Whether a valid token exists with the given id
     */
    public function hasToken(string $tokenId): bool;
}
