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
    public function decode(string $value): string;

    /**
     * {@inheritdoc}
     */
    public function encode(string $value): string;

    /**
     * {@inheritdoc}
     */
    public function verify(string $input, string $value): bool;
}
