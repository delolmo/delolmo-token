<?php

namespace DelOlmo\Token;

/**
 * A token object.
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
interface TokenInterface
{

    /**
     * Returns the token's name
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Returns the token's value
     *
     * @return string
     */
    public function getValue(): string;
}
