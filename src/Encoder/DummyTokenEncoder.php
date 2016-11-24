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
     * {@inheritdoc}
     */
    public function decode(string $value): string
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function encode(string $value): string
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function verify(string $input, string $value): bool
    {
        return $input === $value;
    }

}
