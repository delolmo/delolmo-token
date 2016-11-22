<?php

namespace DelOlmo\Token\Generator;

/**
 * Generates URI-safe tokens.
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class UriSafeTokenGenerator implements TokenGeneratorInterface
{

    /**
     * The amount of entropy collected for each token (in bits).
     *
     * @var int
     */
    private $entropy;

    /**
     * Generates URI-safe tokens.
     *
     * @param int $entropy The amount of entropy collected for each token (in
     * bytes)
     */
    public function __construct($entropy = 64)
    {
        $this->entropy = $entropy;
    }

    /**
     * {@inheritdoc}
     */
    public function generateToken()
    {
        // Generate an URI safe base64 encoded string that does not contain "+",
        // "/" or "=" which need to be URL encoded and make URLs unnecessarily
        // longer.
        $bytes = random_bytes($this->entropy);
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

}
