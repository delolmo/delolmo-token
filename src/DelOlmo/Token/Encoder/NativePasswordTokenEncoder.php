<?php

namespace DelOlmo\Token\Encoder;

/**
 * Description of NativePasswordEncoder
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class NativePasswordTokenEncoder implements TokenEncoderInterface
{
    /**
     * @inherit
     */
    public static function getInfo(string $hash): array
    {
        return password_get_info($hash);
    }

    /**
     * @inherit
     */
    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @inherit
     */
    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT);
    }

    /**
     * @inherit
     */
    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
