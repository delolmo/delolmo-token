<?php

namespace DelOlmo\Token\Manager;

use DelOlmo\Token\Encoder\TokenEncoderInterface as Encoder;
use DelOlmo\Token\Generator\TokenGeneratorInterface as Generator;
use DelOlmo\Token\Storage\TokenStorageInterface as Storage;
use DelOlmo\Token\Encoder\NativePasswordTokenEncoder;
use DelOlmo\Token\Storage\NativeSessionTokenStorage;
use DelOlmo\Token\Generator\UriSafeTokenGenerator;
use DelOlmo\Token\Exception\TokenNotFoundException;

/**
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class ExpirableTokenManager extends TokenManager implements ExpirableTokenManagerInterface
{

    /**
     * @var string
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
     * @var \DelOlmo\Token\Storage\TokenStorageInterface
     */
    protected $storage;

    /**
     * {@inheritdoc}
     */
    public function generateToken(string $tokenId, \DateTime $expiresAt = null): string
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

}
