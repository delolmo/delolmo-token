<?php

namespace DelOlmo\Token\Manager;

use DelOlmo\Token\Encoder\TokenEncoderInterface as Encoder;
use DelOlmo\Token\Generator\TokenGeneratorInterface as Generator;
use DelOlmo\Token\Storage\DeactivableTokenStorageInterface as Storage;
use DelOlmo\Token\Encoder\NativePasswordTokenEncoder;
use DelOlmo\Token\Storage\SessionDeactivableTokenStorage;
use DelOlmo\Token\Generator\UriSafeTokenGenerator;

/**
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class DeactivableTokenManager extends ExpirableTokenManager implements ExpirableTokenManagerInterface
{

    /**
     * @var \DelOlmo\Token\Storage\ExpirableTokenStorageInterface
     */
    protected $storage;

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
        $this->storage = $storage ?? new SessionDeactivableTokenStorage();
        $this->timeout = $timeout ?? new \DateTime(static::TOKEN_TIMEOUT);
    }


    /**
     * {@inheritdoc}
     */
    public function generateToken(string $tokenId, \DateTime $expiresAt = null, bool $active = true): string
    {
        // Value, before hashing, and timeout
        $value = $this->generator->generateToken($tokenId);
        $timeout = $expiresAt ?? new \DateTime(static::TOKEN_TIMEOUT);

        // Hash the value using the provided encoder
        $hash = $this->encoder->hash($value);

        // Store the hashed value
        $this->storage->setToken($tokenId, $hash, $timeout, $active);

        // Return the value before hashing
        return $value;
    }

}
