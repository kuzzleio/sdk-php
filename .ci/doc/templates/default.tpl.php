<?php
use DateTime;

$socket = new WebSocket(new Uri("ws://kuzzle:7512"));
$kuzzle = new KuzzleSdk.Kuzzle(socket);

kuzzle.connect();

[snippet-code]
?>