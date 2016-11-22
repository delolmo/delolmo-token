<?php

namespace DelOlmo\Token\Manager;

use DelOlmo\Token\Encoder\TokenEncoderInterface as Encoder;
use DelOlmo\Token\Generator\TokenGeneratorInterface as Generator;
use DelOlmo\Token\Storage\TokenStorageInterface as Storage;
use DelOlmo\Token\Encoder\NativePasswordTokenEncoder;
use DelOlmo\Token\Storage\SessionTokenStorage;
use DelOlmo\Token\Generator\UriSafeTokenGenerator;
use DelOlmo\Token\Exception\TokenNotFoundException;

/**
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class TokenManager implements TokenManagerInterface
{

    /**
     * @var \DelOlmo\Token\Encoder\TokenEncoderInterface
     */
    protected $encoder;

    /**
     * @var \DelOlmo\Token\Generator\TokenGeneratorInterface
     */
    protected $generator;

    /**
     * @var \DelOlmo\Token\Storage\TokenStorageInterface
     */
    protected $storage;

    /**
     * Constructor.
     *
     * @param \DelOlmo\Token\Generator\TokenGeneratorInterface $generator
     * @param \DelOlmo\Token\Storage\TokenEncoderInterface $encoder
     * @param \DelOlmo\Token\Storage\TokenStorageInterface $storage
     */
    public function __construct(Generator $generator = null, Encoder $encoder = null, Storage $storage = null)
    {
        $this->encoder = $encoder ?? new NativePasswordTokenEncoder();
        $this->generator = $generator ?? new UriSafeTokenGenerator();
        $this->storage = $storage ?? new SessionTokenStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function generateToken(string $tokenId): string
    {
        // Value, before hashing
        $value = $this->generator->generateToken($tokenId);

        // Hash the value using the provided encoder
        $hash = $this->encoder->hash($value);

        // Store the hashed value
        $this->storage->setToken($tokenId, $hash);

        // Return the value before hashing
        return $value;
    }

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
        if (!$this->storage->hasToken($tokenId)) {
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
