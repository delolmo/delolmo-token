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
     * {@inheritdoc}
     */
    public function hash(string $value): string
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * {@inheritdoc}
     */
    public function verify(string $value, string $hash): bool
    {
        return password_verify($value, $hash);
    }
}
