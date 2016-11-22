<?php

namespace DelOlmo\Token\Encoder;

/**
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
interface TokenEncoderInterface
{

    /**
     * {@inheritdoc}
     */
    public function hash(string $value): string;

    /**
     * {@inheritdoc}
     */
    public function verify(string $value, string $hash): bool;
}
