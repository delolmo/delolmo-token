<?php

namespace DelOlmo\Token\Storage\Session;

use DelOlmo\Token\Exception\TokenNotFoundException;
use DelOlmo\Token\Storage\DeactivableTokenStorageInterface;

/**
 * Token storage that uses PHP's native session handling.
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class SessionDeactivableTokenStorage implements DeactivableTokenStorageInterface
{

    /**
     * The namespace used to store values in the session.
     *
     * @var string
     */
    const SESSION_NAMESPACE = '_token';

    /**
     * @var bool
     */
    private $sessionStarted = false;

    /**
     * @var string
     */
    private $namespace;

    /**
     * Initializes the storage with a session namespace.
     *
     * @param string $namespace The namespace under which the token is stored
     * in the session.
     */
    public function __construct($namespace = self::SESSION_NAMESPACE)
    {
        $this->namespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function deactivateToken(string $tokenId)
    {
        return $this->removeToken($tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function deactivateTokens(string $tokenId)
    {
        return $this->deactivateToken($tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(string $tokenId): string
    {
        if (!$this->sessionStarted) {
            $this->startSession();
        }

        if (!$this->hasToken($tokenId)) {
            $str = "No valid token exists for the given id '%s'.";
            $message = sprintf($str, $tokenId);
            throw new TokenNotFoundException($message);
        }

        return (string) $_SESSION[$this->namespace][$tokenId]['value'];
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(string $tokenId, string $value, \DateTime $expiresAt, bool $active)
    {
        if (!$this->sessionStarted) {
            $this->startSession();
        }

        $_SESSION[$this->namespace][$tokenId] = [
            'value' => $value,
            'expiresAt' => $expiresAt,
            'active' => $active
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function hasToken(string $tokenId): bool
    {
        if (!$this->sessionStarted) {
            $this->startSession();
        }

        return isset($_SESSION[$this->namespace][$tokenId]) &&
                isset($_SESSION[$this->namespace][$tokenId]['value']) &&
                isset($_SESSION[$this->namespace][$tokenId]['expiresAt']) &&
                isset($_SESSION[$this->namespace][$tokenId]['active']) &&
                is_string($_SESSION[$this->namespace][$tokenId]['value']) &&
                $_SESSION[$this->namespace][$tokenId]['expiresAt'] instanceof \DateTime &&
                is_bool($_SESSION[$this->namespace][$tokenId]['active']) &&
                $_SESSION[$this->namespace][$tokenId]['expiresAt'] >= new \DateTime() &&
                $_SESSION[$this->namespace][$tokenId]['expiresAt'] === true;
    }

    /**
     * {@inheritdoc}
     */
    public function removeToken(string $tokenId)
    {
        if (!$this->sessionStarted) {
            $this->startSession();
        }

        $token = isset($_SESSION[$this->namespace][$tokenId]) ?
                (string) $_SESSION[$this->namespace][$tokenId] :
                null;

        unset($_SESSION[$this->namespace][$tokenId]);

        return $token;
    }

    /**
     * Simple helper function to start the session.
     *
     * @return void
     */
    private function startSession()
    {
        if (\PHP_SESSION_NONE === session_status()) {
            session_start();
        }

        $this->sessionStarted = true;
    }

}
