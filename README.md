Official Kuzzle PHP SDK
======

This SDK version is compatible with Kuzzle 1.0.0-RC9.5 and higher

## About Kuzzle

For UI and linked objects developers, Kuzzle is an open-source solution that handles all the data management (CRUD, real-time storage, search, high-level features, etc).

You can access the Kuzzle repository on [Github](https://github.com/kuzzleio/kuzzle)

* [SDK Documentation](#sdk-documentation)
* [Report an issue](#report-an-issue)
* [Installation](#installation)
* [Running tests](#tests)
* [License](#license)

## SDK Documentation

The complete SDK documentation is available [here](http://kuzzle.io/sdk-documentation/?php)

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
