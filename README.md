#delolmo-token

DelOlmo\Token provides a set of classes and interfaces to manage tokens
(CSRF, account activation, password reset, etc.). It provides two main classes
`TokenManager` and `ExpirableTokenManager`, easily extensible, to generate,
validate, and refresh tokens. Its main goal is to provide a code base for others
to write their own token managers. It does, however, ship with a ready to use
cross-site request forgery (CSRF) token manager.


#Warning

This library is actively being developed. Please don't use it in production
environments yet.

#Installation and autoloading

This package is installable and PSR-4 autoloadable via Composer as 
`delolmo/delolmo-token`.

Alternatively, download a release, or clone this repository, then map the 
DelOlmo\Token\ namespace to the package src/ directory.

#Dependencies

This package requires PHP 7.0 or later. As a matter of principle, the latest
version of PHP should always be used.

This library is a standalone component with no third-party dependencies. This
allows compliance with community standards and encourages reusability. 

#Brief description of inner workings

Token managers depend on three basic components:

###The Storage component

The 


