[![Build Status](https://travis-ci.org/kuzzleio/sdk-php.svg?branch=master)](https://travis-ci.org/kuzzleio/sdk-php) [![codecov.io](http://codecov.io/github/kuzzleio/sdk-php/coverage.svg?branch=master)](http://codecov.io/github/kuzzleio/sdk-php?branch=master)

Official Kuzzle PHP SDK
======

## About Kuzzle

A backend software, self-hostable and ready to use to power modern apps.

You can access the Kuzzle repository on [Github](https://github.com/kuzzleio/kuzzle)

* [SDK Documentation](#sdk-documentation)
* [Report an issue](#report-an-issue)
* [Installation](#installation)
* [Basic usage](#basic-usage)
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

## Basic usage

```php
<?php

use \Kuzzle\Kuzzle;
use \Kuzzle\Document;

$kuzzle = new Kuzzle('localhost');
$collection = $kuzzle->collection('bar', 'foo');

$firstDocument = new Document($collection, 'john', ['name' => 'John', 'age' => 42]);
$secondDocument = new Document($collection, 'michael', ['name' => 'Michael', 'age' => 36]);

$firstDocument->save(['refresh' => 'wait_for']);
$secondDocument->save(['refresh' => 'wait_for']);

$result = $collection->search(['sort' => [['age' => 'asc']]]);
foreach ($result->getDocuments() as $document) {
    $content = $document->getContent();
    echo "Name: {$content['name']}, age: {$content['age']}\n";
}
```

## <a name="tests"></a> Running Tests

```
php ./vendor/bin/phpcs -p -n --standard=PSR2 src
php ./vendor/bin/phpunit
```

## License

[Apache 2](LICENSE.md)
