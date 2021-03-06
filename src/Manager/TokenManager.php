<?php

namespace DelOlmo\Token\Manager;

use DelOlmo\Token\Encoder\DummyTokenEncoder;
use DelOlmo\Token\Encoder\TokenEncoderInterface as Encoder;
use DelOlmo\Token\Exception\TokenAlreadyExistsException;
use DelOlmo\Token\Generator\TokenGeneratorInterface as Generator;
use DelOlmo\Token\Generator\UriSafeTokenGenerator;
use DelOlmo\Token\Storage\Session\SessionTokenStorage;
use DelOlmo\Token\Storage\TokenStorageInterface as Storage;

/**
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class TokenManager extends AbstractTokenManager implements TokenManagerInterface
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
     * @param \DelOlmo\Token\Storage\TokenStorageInterface $storage
     * @param \DelOlmo\Token\Encoder\TokenEncoderInterface $encoder
     * @param \DelOlmo\Token\Generator\TokenGeneratorInterface $generator
     */
    public function __construct(Storage $storage = null, Encoder $encoder = null, Generator $generator = null)
    {
        $this->encoder = $encoder ?? new DummyTokenEncoder();
        $this->generator = $generator ?? new UriSafeTokenGenerator();
        $this->storage = $storage ?? new SessionTokenStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function generateToken(string $tokenId): string
    {
        // Prevent overwriting an already existing valid token
        if ($this->hasToken($tokenId)) {
            $str = "A valid token already exists for the given id '%s'.";
            $message = sprintf($str, $tokenId);
            throw new TokenAlreadyExistsException($message);
        }

        // Return value, before hashing
        return $this->refreshToken($tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshToken(string $tokenId): string
    {
        // Value, before hashing
        $value = $this->generator->generateToken($tokenId);

        // Hash the value using the provided encoder
        $encoded = $this->encoder->encode($value);

        // Store the hashed value
        $this->storage->setToken($tokenId, $encoded);

        // Return the value before hashing
        return $value;
    }

}
