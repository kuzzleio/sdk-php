
<?php

use \Kuzzle\Kuzzle;


$kuzzle = new Kuzzle('localhost');

try {
  $status = $kuzzle->memoryStorage()->hset('key', 'field', 'value');
}
catch (ErrorException $e) {

}
