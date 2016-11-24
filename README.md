#delolmo-token

DelOlmo\Token provides a set of classes and interfaces to manage tokens (CSRF, account activation, password reset, etc.). It provides two main classes `TokenManager` and `ExpirableTokenManager`, easily extensible, to generate, validate, and refresh tokens. Its main goal is to provide a code base for others to write their own token managers. It does, however, ship with a ready to use cross-site request forgery (CSRF) token manager.

##Disclaimer

This library is actively being developed. Please don't use it in production environments yet.

##Installation and autoloading

This package is installable and PSR-4 autoloadable via Composer as `delolmo/delolmo-token`.

Alternatively, download a release, or clone this repository, then map the `DelOlmo\Token\` namespace to the package `src/` directory.

##Dependencies

This package requires PHP 7.0 or later. As a matter of principle, the latest version of PHP should always be used.

This library is a standalone component with no third-party dependencies. Threfore, it does not rely on other libraries to achieve its goals. This allows compliance with community standards and encourages reusability.

##Design principles

The need for this component is based on two problems that I have encountered while working with several popular libraries:

1. Regarding tokens, most of the code out there is tightly coupled into bigger components. E.g., the `zendframework/zend-form` component implements a `Zend\Form\Element\Csrf` object, but reusing this object in other libraries is far from obvious. The `symfony/security-core` package, on the other hand, addresses the problem of authenticating via an API key with yet another custom implementation of a token object `Symfony\Component\Security\Core\Authentication\Token\TokenInterface`. The fact is, however, that all tokens, whatever their specific use case - CSRF tokens, API tokens, reset tokens, etc. - are generated and stored in a similar fashion.
2. One of the main tradeoffs of how popular libraries/frameworks work with tokens is that they don't take into account some use cases in which hashing/encoding a token is required or desirable. For example, when working with password reset tokens, tokens should be hashed before storing them in a database (for the same reasons that actual passwords are). Most of the times, this task is completely up to the developer. The `symfony/security-csrf` component is missing this *encoder/decoder* component. The `zend/zend-crypt` package provides a `Zend\Crypt\BlockCipher` and a `Zend\Crypt\Bcrypt` element, both of which should easily fullfill this need but which are not as easily reusable in the `symfony/security-csrf` package.

Token generation and validation should be therefore refactored as an independent component and follow a framework-agnostic model. This is precisely the goal of this library. To do so, the library is built around an object, which can be referred to as the **Manager**, that is responsible for handling all the logic of generating, storing and verifying tokens. **Managers** are built on top of three independent components:

- The **Storage** component. Responsible for retrieving, storing, updating and deleting tokens, the storage component is completely unaware of how tokens are hashed/encoded/decoded. Its only 
- The **Encoder** component
- The **Generator** component

###The Storage component

...


