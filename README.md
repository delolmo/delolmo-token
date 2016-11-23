#delolmo-token

DelOlmo\Token provides a set of classes and interfaces to manage tokens
(CSRF, account activation, password reset, etc.). It provides two main classes
`TokenManager` and `ExpirableTokenManager`, easily extensible, to generate,
validate, and refresh tokens. Its main goal is to provide a code base for others
to write their own token managers. It does, however, ship with a ready to use
cross-site request forgery (CSRF) token manager.

This library is a standalone component with no third-party dependencies.

##Warning

This library is actively being developed. Please don't use it in production
environments yet.

##Brief description of inner workings

Token managers depend on three basic components:

* A storage component, used to store, read, update and delete tokens. The
storage component is completely unaware of how tokens are manipulated. Its only
concern is to store and retrieve token data when asked for.
* An encoder component, used to hash or encode/decode tokens. Certain use cases
require hashing a token before storing it, e.g., reset password tokens should 
be hashed before being stored in a database (for exactly the same reasons 
passwords are).
* A generator component, used to generate token values.

##The CsrfTokenManager


