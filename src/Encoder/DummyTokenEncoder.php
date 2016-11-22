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
    public function hash(string $value): string
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function verify(string $value, string $hash): bool
    {
        return $value === $hash;
    }

}
