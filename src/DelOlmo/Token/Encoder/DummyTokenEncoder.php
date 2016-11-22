<?php

namespace DelOlmo\Token\Encoder;

/**
 * Description of DummyTokenEncoder
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class DummyTokenEncoder implements TokenEncoderInterface
{
    /**
     * @inherit
     */
    public static function getInfo(string $hash): array
    {
        return null;
    }

    /**
     * @inherit
     */
    public static function hash(string $password): string
    {
        return $password;
    }

    /**
     * @inherit
     */
    public static function needsRehash(string $hash): bool
    {
        return false;
    }

    /**
     * @inherit
     */
    public static function verify(string $password, string $hash): bool
    {
        return $password === $hash;
    }

}
