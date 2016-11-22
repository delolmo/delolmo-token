<?php

namespace DelOlmo\Token;

/**
 * A token object.
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class Token implements TokenInterface
{

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $value;

    /**
     * Constructor
     *
     * @param string $id
     * @param string $value
     */
    public function __construct(string $id, string $value)
    {
        $this->id = $id;
        $this->value = $value;
    }

    /**
     * Devuelve el valor del token
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(): string
    {
        return $this->value;
    }

}
