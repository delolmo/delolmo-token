<?php

namespace DelOlmo\Token\Manager;

/**
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
interface ExpirableTokenManagerInterface
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
     * @param \DateTime $expiresAt The date and time on which the token expires
     * @param bool $active Whether the token must be stored as active or not
     * @return string The generated value of the token, before hashing
     * @throws DelOlmo\Token\Exception\TokenAlreadyExistsException if a valid
     * token already exists with the given token id
     */
    public function generateToken(string $tokenId, \DateTime $expiresAt, bool $active): string;

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
     * @param $value The raw value of the token
     * @return bool Whether the given token is valid
     */
    public function isTokenValid(string $tokenId, string $value): bool;

    /**
     * Generates a new value for the given token id.
     *
     * This method will generate a new token for the given token id, whether
     * or not the token existed previously. It can be used to enforce once-only
     * tokens in environments with high security needs.
     *
     * @param string $tokenId
     * @param \DateTime $expiresAt The date and time on which the token expires
     * @param bool $active Whether the token must be stored as active or not
     * @return string The generated value of the token, before hashing
     */
    public function refreshToken(string $tokenId, \DateTime $expiresAt, bool $active): string;

    /**
     * Removes a token from storage with the given id, if one exists.
     *
     * @param string $tokenId A token id.
     * @return string|null Returns the removed hashed token value if one
     * existed, NULL otherwise
     */
    public function removeToken(string $tokenId);
}
