<?php

namespace DelOlmo\Token\Manager;

use DelOlmo\Token\Encoder\TokenEncoderInterface as Encoder;
use DelOlmo\Token\Generator\TokenGeneratorInterface as Generator;
use DelOlmo\Token\Storage\ExpirableTokenStorageInterface as Storage;
use DelOlmo\Token\Encoder\NativePasswordTokenEncoder;
use DelOlmo\Token\Storage\SessionExpirableTokenStorage;
use DelOlmo\Token\Generator\UriSafeTokenGenerator;

/**
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class ExpirableTokenManager extends TokenManager implements ExpirableTokenManagerInterface
{

    /**
     * @var string The default interval on which tokens expire by default
     */
    const TOKEN_TIMEOUT = '+1 day';

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
     * @param \DelOlmo\Token\Generator\TokenGeneratorInterface $generator
     * @param \DelOlmo\Token\Encoder\TokenEncoderInterface $encoder
     * @param \DelOlmo\Token\Storage\ExpirableTokenStorageInterface $storage
     * @param \DateTime $timeout
     */
    public function __construct(Generator $generator = null, Encoder $encoder = null, Storage $storage = null, \DateTime $timeout = null)
    {
        $this->encoder = $encoder ?? new NativePasswordTokenEncoder();
        $this->generator = $generator ?? new UriSafeTokenGenerator();
        $this->storage = $storage ?? new SessionExpirableTokenStorage();
        $this->timeout = $timeout ?? new \DateTime(static::TOKEN_TIMEOUT);
    }

    /**
     * {@inheritdoc}
     */
    public function generateToken(string $tokenId, \DateTime $expiresAt = null): string
    {
        // Value, before hashing, and timeout
        $value = $this->generator->generateToken($tokenId);
        $timeout = $expiresAt ?? $this->timeout;

        // Hash the value using the provided encoder
        $hash = $this->encoder->hash($value);

        // Store the hashed value
        $this->storage->setToken($tokenId, $hash, $timeout);

        // Return the value before hashing
        return $value;
    }

}
