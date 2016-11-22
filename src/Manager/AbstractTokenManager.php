<?php

namespace DelOlmo\Token\Manager;

use DelOlmo\Token\Exception\TokenNotFoundException;

/**
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
abstract class AbstractTokenManager
{

    /**
     * {@inheritdoc}
     */
    public function getToken(string $tokenId): string
    {
        // If the given $tokenId does not exist
        if (!$this->storage->hasToken($tokenId)) {
            $str = "No valid token exists for the given id '%s'.";
            $message = sprintf($str, $tokenId);
            throw new TokenNotFoundException($message);
        }

        return $this->storage->getToken($tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function hasToken(string $tokenId): bool
    {
        return $this->storage->hasToken($tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function isTokenValid(string $tokenId, string $value): bool
    {
        // Return false if no token exists with the given token id
        if (!$this->hasToken($tokenId)) {
            return false;
        }

        // Read the hashed value of the stored token
        $hash = $this->storage->getToken($tokenId);

        // Whether or not the hashed/encoded value and the given value match
        return $this->encoder->verify($value, $hash);
    }

    /**
     * {@inheritdoc}
     */
    public function removeToken(string $tokenId)
    {
        return $this->storage->removeToken($tokenId);
    }
}
