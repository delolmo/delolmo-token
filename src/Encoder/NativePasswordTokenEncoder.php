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
    public function decode(string $value): string
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function encode(string $value): string
    {
        return \password_hash($value, \PASSWORD_DEFAULT);
    }

    /**
     * {@inheritdoc}
     */
    public function verify(string $input, string $value): bool
    {
        return \password_verify($input, $value);
    }

}
