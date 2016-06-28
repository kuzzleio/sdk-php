<?php

class MoneyTest extends \PHPUnit_Framework_TestCase
{
    // ...

    public function testCanBeNegated()
    {
        // Arrange
        $a = new \Kuzzle\Kuzzle('http://127.0.0.1:7511');

        // Assert
        $this->assertInstanceOf('\Kuzzle\Kuzzle', $a);
    }

    // ...
}
