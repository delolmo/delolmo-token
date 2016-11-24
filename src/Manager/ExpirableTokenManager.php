<?php

namespace DelOlmo\Token\Manager;

use DelOlmo\Token\Encoder\DummyTokenEncoder;
use DelOlmo\Token\Encoder\TokenEncoderInterface as Encoder;
use DelOlmo\Token\Exception\TokenAlreadyExistsException;
use DelOlmo\Token\Generator\TokenGeneratorInterface as Generator;
use DelOlmo\Token\Generator\UriSafeTokenGenerator;
use DelOlmo\Token\Storage\ExpirableTokenStorageInterface as Storage;
use DelOlmo\Token\Storage\Session\SessionExpirableTokenStorage;

/**
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class ExpirableTokenManager extends AbstractTokenManager implements ExpirableTokenManagerInterface
{

    /**
     * @var string The default interval on which tokens expire by default
     */
    const TOKEN_TIMEOUT = '+1 day';

    /**
     * @var \DelOlmo\Token\Encoder\TokenEncoderInterface
     */
    protected $encoder;

    /**
     * @var \DelOlmo\Token\Generator\TokenGeneratorInterface
     */
    protected $generator;

    /**
     * @var \DelOlmo\Token\Storage\ExpirableTokenStorageInterface
     */
    protected $storage;

    /**
     * @var \DateTime The date on which ExpirableToken objects expire by default
     */
    protected $timeout;

    /**
     * Constructor.
     *
     * @param \DelOlmo\Token\Storage\ExpirableTokenStorageInterface $storage
     * @param \DelOlmo\Token\Encoder\TokenEncoderInterface $encoder
     * @param \DelOlmo\Token\Generator\TokenGeneratorInterface $generator
     * @param \DateTime $timeout
     */
    public function __construct(Storage $storage = null, Encoder $encoder = null, Generator $generator = null, \DateTime $timeout = null)
    {
        $this->encoder = $encoder ?? new DummyTokenEncoder();
        $this->generator = $generator ?? new UriSafeTokenGenerator();
        $this->storage = $storage ?? new SessionExpirableTokenStorage();
        $this->timeout = $timeout ?? new \DateTime(static::TOKEN_TIMEOUT);
    }

    /**
     * {@inheritdoc}
     */
    public function generateToken(string $tokenId, \DateTime $expiresAt = null): string
    {
        // Prevent overwriting an already existing token
        if ($this->hasToken($tokenId)) {
            $str = "A valid token already exists for the given id '%s'.";
            $message = sprintf($str, $tokenId);
            throw new TokenAlreadyExistsException($message);
        }

        // Return the value before hashing
        return $this->refreshToken($tokenId, $expiresAt);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshToken(string $tokenId, \DateTime $expiresAt = null): string
    {
        // Value, before hashing, and timeout
        $value = $this->generator->generateToken($tokenId);
        $timeout = $expiresAt ?? $this->timeout;

        // Encode the value using the provided encoder
        $encoded = $this->encoder->encode($value);

        // Store the hashed value
        $this->storage->setToken($tokenId, $encoded, $timeout);

        // Return the value before hashing
        return $value;
    }

}
