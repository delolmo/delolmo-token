<?php

namespace DelOlmo\Token\Manager;

use DelOlmo\Token\Encoder\DummyTokenEncoder;
use DelOlmo\Token\Generator\UriSafeTokenGenerator;
use DelOlmo\Token\Storage\Session\SessionTokenStorage;

/**
 * An object that manages cross-site request forgery (CSRF) tokens.
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class CsrfTokenManager extends TokenManager {

    /**
     * Constructor
     */
    public function __construct()
    {
        $generator = new UriSafeTokenGenerator(64);
        $encoder = new DummyTokenEncoder();
        $storage = new SessionTokenStorage('_csrf');
        
        parent::__construct($generator, $encoder, $storage);
    }
    
}
