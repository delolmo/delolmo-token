delolmo-token
=============

DelOlmo\Token provides a set of classes and interfaces to manage tokens
(CSRF, account activation, password reset, etc.). It provides two main classes
`TokenManager` and `ExpirableTokenManager`, easily extensible, to generate,
validate, and refresh tokens. Its main goal is to provide a code base for others
to write their own token managers. That being said, it ships with a ready to use
cross-site request forgery (CSRF) token manager.

This library is a standalone component with no third-party dependencies.

Installation
------------

Install this package via `Composer`:

    composer require delolmo/delolmo-invoice "~1.0"

Usage
-----

Token 

