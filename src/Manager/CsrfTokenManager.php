<?php

namespace DelOlmo\Token\Manager;

use DelOlmo\Token\Encoder\DummyTokenEncoder;
use DelOlmo\Token\Generator\UriSafeTokenGenerator;
use DelOlmo\Token\Storage\Session\SessionExpirableTokenStorage;

/**
 * An object to manager cross-site request forgery (CSRF) tokens.
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class CsrfTokenManager extends ExpirableTokenManager
{

    /**
     * @var string The default interval on which tokens expire by default
     */
    const TOKEN_TIMEOUT = '+24 hour';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $generator = new UriSafeTokenGenerator(64);
        $encoder = new DummyTokenEncoder();
        $storage = new SessionExpirableTokenStorage('_csrf');
        
        parent::__construct($storage, $encoder, $generator);
    }
}
