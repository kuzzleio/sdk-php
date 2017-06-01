Official Kuzzle PHP SDK
======

## About Kuzzle

A backend software, self-hostable and ready to use to power modern apps.

You can access the Kuzzle repository on [Github](https://github.com/kuzzleio/kuzzle)

* [SDK Documentation](#sdk-documentation)
* [Report an issue](#report-an-issue)
* [Installation](#installation)
* [Running tests](#tests)
* [License](#license)

## SDK Documentation

The complete SDK documentation is available [here](http://docs.kuzzle.io/sdk-reference/)

## Report an issue

Use following meta repository to report issues on SDK:

https://github.com/kuzzleio/kuzzle-sdk/issues

## Installation

This SDK can be used in any project using composer:

```
composer require kuzzleio/kuzzle-sdk
```

## <a name="tests"></a> Running Tests

```
php ./vendor/bin/phpcs -p -n --standard=PSR2 src
php ./vendor/bin/phpunit
```

## License

[Apache 2](LICENSE.md)
