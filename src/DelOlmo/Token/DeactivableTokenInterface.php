<?php

namespace DelOlmo\Token;

/**
 * An interface for a token that can be deactivated.
 *
 * A token can be flagged as inactive for multiple reasons, e.g., when it has
 * already been used.
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
interface DeactivableTokenInterface extends ExpirableTokenInterface
{

    /**
     * Whether the Token is still active or not
     *
     * @return type
     */
    public function isActive(): bool;
}
