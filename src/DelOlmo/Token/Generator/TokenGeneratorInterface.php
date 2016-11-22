<?php

namespace DelOlmo\Token\Generator;

/**
 * Generates tokens.
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
interface TokenGeneratorInterface
{

    /**
     * Generates a token.
     *
     * @return string The generated token
     */
    public function generateToken();
}
