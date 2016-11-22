<?php

namespace DelOlmo\Token\Encoder;

/**
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
interface TokenEncoderInterface
{

    /**
     * @inherit
     */
    public static function getInfo(string $hash): array;

    /**
     * @inherit
     */
    public static function hash(string $password): string;

    /**
     * @inherit
     */
    public static function needsRehash(string $hash): bool;

    /**
     * @inherit
     */
    public static function verify(string $password, string $hash): bool;
}
