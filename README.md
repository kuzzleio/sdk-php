Official Kuzzle PHP SDK 
======

This SDK version is compatible with Kuzzle 1.0.0-beta.5 and higher

## About Kuzzle

For UI and linked objects developers, Kuzzle is an open-source solution that handles all the data management (CRUD, real-time storage, search, high-level features, etc).

You can access the Kuzzle repository on [Github](https://github.com/kuzzleio/kuzzle)

* [SDK Documentation](#sdk-documentation)
* [Installation](#installation)
  * [Composer](#composer)
    * [Basic usage](#basic-usage)
    * [Bundle Symfony](#bundle)
* [License](#license)

## SDK Documentation

The complete SDK documentation is available [here](http://kuzzle.io/sdk-documentation/?php)

## Installation

This SDK can be used in any project using composer.
You can also find a bridge bundle for Symfony [here](https://github.com/kuzzleio/kuzzle-symfony-bridge)

### <a name="composer"></a> Composer

```
composer require kuzzleio/kuzzle-sdk
```

#### <a name="basic-usage"></a> Basic usage

```php
<?php

$kuzzle = new \Kuzzle\Kuzzle('http://localhost:7511');
$collection = $kuzzle->dataCollectionFactory('mycollection', 'myindex');

$myDocument = [
  'name' => 'Rick Astley',
  'birthDate' => '1966/02/06',
  'mainActivity' => 'Singer',
  'website' => 'http://www.rickastley.co.uk',
  'comment' => 'Never gonna give you up, never gonna let you down'
];

try {
    $result = $collection->createDocument($myDocument);
    /*
    'result' is a \Kuzzle\Document object
    */
}
catch (Exception $error) {
    // handle error...
}

```

#### <a name="bundle"></a> Bundle Symfony

You can find a bridge bundle for Symfony [here](https://github.com/kuzzleio/kuzzle-symfony-bridge)


```
composer require kuzzleio/kuzzle-symfony-bridge
```

## License

[Apache 2](LICENSE.md)