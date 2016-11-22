<?php

namespace DelOlmo\Token\Manager;

/**
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
interface TokenManagerInterface
{

    /**
     * Returns the stored value of a token.
     *
     * @param string $tokenId The token id
     * @return string The stored hashed value of the token
     * @throws \DelOlmo\Token\Exception\TokenNotFoundException If no valid
     * token exists for given token id
     */
    public function getToken(string $tokenId): string;

    /**
     * Generates a new token and stores it.
     *
     * @param string $tokenId
     * @return string The generated value of the token, before hashing
     */
    public function generateToken(string $tokenId): string;

    /**
     * Whether or not a valid token exists for the given token id.
     *
     * @param string $tokenId
     * @return bool
     */
    public function hasToken(string $tokenId): bool;

    /**
     * Whether or not the given token is valid.
     *
     * @param $tokenId The token id
     * @param $value The unhashed value of the token
     * @return bool
     */
    public function isTokenValid(string $tokenId, string $value): bool;

    /**
     * Removes a token from storage with the given id, if one exists.
     *
     * @param string $tokenId A token id.
     * @return string|null Returns the removed hashed token value if one
     * existed, NULL otherwise
     */
    public function removeToken(string $tokenId);

}
