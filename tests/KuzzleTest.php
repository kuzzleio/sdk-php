<?php

use Kuzzle\Util\CurlRequest;

class KuzzleTest extends \PHPUnit_Framework_TestCase
{
    const FAKE_KUZZLE_HOST = '127.0.0.1';
    const FAKE_KUZZLE_URL = 'http://127.0.0.1:7512';

    public function testSimpleConstructor()
    {
        // Arrange
        $host = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($host, ['port' => 1234]);

            // Assert type
            $this->assertInstanceOf('\Kuzzle\Kuzzle', $kuzzle);

            // Assert Url
            $this->assertAttributeEquals('http://' . $host . ':1234', 'url', $kuzzle);

            // Assert if one (random) route is loaded
            $routesDescription = $this->readAttribute($kuzzle, 'routesDescription');
            $routesNow = [
                'method' => 'get',
                'name' => 'now',
                'route' =>  '/_now'
            ];

            $this->assertEquals($routesNow, $routesDescription['server']['now']);
        }
        catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testMemoryStorage()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {

            $kuzzle = new \Kuzzle\Kuzzle($url);
            $memoryStorage = $kuzzle->memoryStorage();

            // Assert type
            $this->assertInstanceOf('\Kuzzle\MemoryStorage', $memoryStorage);

            // Assert Properties
            $this->assertAttributeEquals($kuzzle, 'kuzzle', $memoryStorage);
        }
        catch (Exception $e) {
            $this->fail('KuzzleTest::testMemoryStorage => Should not raise an exception');
        }
    }

    public function testSetJwtToken()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;
        $token = uniqid();

        $kuzzle = new \Kuzzle\Kuzzle($url);
        $kuzzle->setJwtToken($token);

        // Assert Properties
        $this->assertAttributeEquals($token, 'jwtToken', $kuzzle);
    }

    public function testSetRequestHandler()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        $handler = new CurlRequest();

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->setRequestHandler($handler);

            // Assert Properties
            $this->assertAttributeEquals($handler, 'requestHandler', $kuzzle);
        }
        catch (Exception $e) {
            $this->fail('KuzzleTest::testSetRequestHandler => Should not raise an exception');
        }
    }

    public function testRouteDescriptionWithoutConfigurationFile()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;
        $routeDescriptionFile = 'fakeFile.json';

        try {
            new \Kuzzle\Kuzzle($url, ['routesDescriptionFile' => $routeDescriptionFile]);
            $this->fail('KuzzleTest::testRouteDescription => Should raise an exception due to missing configuration file');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('Exception', $e);
            $this->assertEquals('Unable to find http routes configuration file (' . $routeDescriptionFile . ')', $e->getMessage());
        }
    }

    public function testRouteDescriptionWithBadConfigurationFile()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;
        $routeDescriptionFile = '../tests/config/mockRoutes.json';

        try {
            new \Kuzzle\Kuzzle($url, ['routesDescriptionFile' => $routeDescriptionFile]);
            $this->fail('KuzzleTest::testRouteDescription => Should raise an exception due to bad format of configuration file');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('Exception', $e);
            $this->assertEquals('Unable to parse http routes configuration file (' . $routeDescriptionFile . '): should return an array', $e->getMessage());
        }
    }

    public function testBuildQueryArgs()
    {
        /**
         * @todo
         */
    }

    public function testEmitRestRequest()
    {
        $curlRequestHandler = $this
            ->getMockBuilder('\Kuzzle\Util\CurlRequest')
            ->setMethods(['execute'])
            ->setConstructorArgs([])
            ->getMock();

        $kuzzle = new \Kuzzle\Kuzzle(self::FAKE_KUZZLE_HOST, ['requestHandler' => $curlRequestHandler]);

        $volatile = ['sdkVersion' => $kuzzle->getSdkVersion()];

        $httpRequest = [
            'request' => [
                'headers' => ['authorization' => 'Bearer ' . uniqid()],
                'body' => ['foo' => 'bar'],
                'volatile' => $volatile
            ],
            'route' => '/foo/bar',
            'method' => 'POST',
            'query_parameters' => ['foo' => 'bar']
        ];

        $body = json_encode($httpRequest['request']['body'], JSON_FORCE_OBJECT);

        $curlRequest = [
            'url' => self::FAKE_KUZZLE_URL . $httpRequest['route'],
            'method' => $httpRequest['method'],
            'headers' => [
                'Content-type: application/json',
                'Authorization: ' . $httpRequest['request']['headers']['authorization'],
                'X-Kuzzle-Volatile: ' . json_encode($volatile),
                'Content-length: ' . strlen($body)
            ],
            'body' => $body,
            'query_parameters' => ['foo' => 'bar']
        ];

        $reflection = new \ReflectionClass(get_class($kuzzle));
        $method = $reflection->getMethod('emitRestRequest');
        $method->setAccessible(true);

        $curlRequestHandler
            ->expects($this->once())
            ->method('execute')
            ->with($curlRequest)
            ->willReturn(['error' => '', 'response' => '{"foo": "bar"}']);

        try {
            $result = $method->invokeArgs($kuzzle, [$httpRequest]);

            $this->assertEquals(["foo" => "bar"], $result);
        }
        catch (\Exception $e) {
            $this->fail($e);
        }
    }

    public function testEmitRestRequestWithHTTPError()
    {
        $curlRequestHandler = $this
            ->getMockBuilder('\Kuzzle\Util\CurlRequest')
            ->setMethods(['execute'])
            ->setConstructorArgs([])
            ->getMock();

        $kuzzle = new \Kuzzle\Kuzzle(self::FAKE_KUZZLE_HOST, ['requestHandler' => $curlRequestHandler]);

        $volatile = ['sdkVersion' => $kuzzle->getSdkVersion()];

        $httpRequest = [
            'request' => [
                'headers' => ['authorization' => 'Bearer ' . uniqid()],
                'body' => ['foo' => 'bar'],
                'volatile' => $volatile
            ],
            'route' => '/foo/bar',
            'method' => 'POST',
            'query_parameters' => []
        ];

        $body = json_encode($httpRequest['request']['body'], JSON_FORCE_OBJECT);

        $curlRequest = [
            'url' => self::FAKE_KUZZLE_URL . $httpRequest['route'],
            'method' => $httpRequest['method'],
            'headers' => [
                'Content-type: application/json',
                'Authorization: ' . $httpRequest['request']['headers']['authorization'],
                'X-Kuzzle-Volatile: ' . json_encode($volatile),
                'Content-length: ' . strlen($body)
            ],
            'body' => $body,
            'query_parameters' => []
        ];

        $reflection = new \ReflectionClass(get_class($kuzzle));
        $method = $reflection->getMethod('emitRestRequest');
        $method->setAccessible(true);

        $curlRequestHandler
            ->expects($this->once())
            ->method('execute')
            ->with($curlRequest)
            ->willReturn(['error' => 'HTTP Error', 'response' => '']);

        $triggerCount = 0;
        $triggerArgs = [];
        $errListener = function() use (&$triggerArgs, &$triggerCount){
          $triggerArgs[$triggerCount++] = func_get_args();
        };
        $kuzzle->addListener('queryError', $errListener);

        try {
            $method->invokeArgs($kuzzle, [$httpRequest]);

            $this->fail("KuzzleTest::testEmitRestRequestWithHTTPError => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('ErrorException', $e);
            $this->assertEquals('HTTP Error', $e->getMessage());
            $this->assertEquals($triggerCount, 1);
            $this->assertEquals('HTTP Error', $triggerArgs[0][0]);
        }
    }

    public function testEmitRestRequestWithKuzzleError()
    {
        $curlRequestHandler = $this
            ->getMockBuilder('\Kuzzle\Util\CurlRequest')
            ->setMethods(['execute'])
            ->setConstructorArgs([])
            ->getMock();

        $kuzzle = new \Kuzzle\Kuzzle(self::FAKE_KUZZLE_HOST, ['requestHandler' => $curlRequestHandler]);

        $reflection = new \ReflectionClass(get_class($kuzzle));
        $emitRestRequest = $reflection->getMethod('emitRestRequest');
        $emitRestRequest->setAccessible(true);

        $volatile = ['sdkVersion' => $kuzzle->getSdkVersion()];

        $httpRequest = [
            'request' => [
                'headers' => ['authorization' => 'Bearer ' . uniqid()],
                'body' => ['foo' => 'bar'],
                'volatile' => $volatile
            ],
            'route' => '/foo/bar',
            'method' => 'POST',
            'query_parameters' => []
        ];

        $body = json_encode($httpRequest['request']['body'], JSON_FORCE_OBJECT);

        $curlRequest = [
            'url' => self::FAKE_KUZZLE_URL . $httpRequest['route'],
            'method' => $httpRequest['method'],
            'headers' => [
                'Content-type: application/json',
                'Authorization: ' . $httpRequest['request']['headers']['authorization'],
                'X-Kuzzle-Volatile: ' . json_encode($volatile),
                'Content-length: ' . strlen($body)
            ],
            'body' => $body,
            'query_parameters' => []
        ];

        $curlRequestHandler
            ->expects($this->once())
            ->method('execute')
            ->with($curlRequest)
            ->willReturn(['error' => '', 'response' => '{"error": {"message": "Kuzzle Error"}}']);

        $triggerCount = 0;
        $triggerArgs = [];
        $errListener = function() use (&$triggerArgs, &$triggerCount){
          $triggerArgs[$triggerCount++] = func_get_args();
        };
        $kuzzle->addListener('queryError', $errListener);

        try {
            $emitRestRequest->invokeArgs($kuzzle, [$httpRequest]);

            $this->fail("KuzzleTest::testEmitRestRequestWithKuzzleError => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertEquals('Kuzzle Error', $e->getMessage());
            $this->assertInstanceOf('ErrorException', $e);
            $this->assertEquals($triggerCount, 1);
            $this->assertEquals(['message' => 'Kuzzle Error'], $triggerArgs[0][0]);
        }
    }

    public function testConvertRestRequest()
    {
        $request = [
            'route' => '/:index/:collection/:_id/:custom/_foobar',
            'method' => 'post',
            'controller' => 'foo',
            'action' => 'bar',
            'collection' => 'my-collection',
            'index' => 'my-index',
            '_id' => 'my-id',
            'body' => ['foo' => 'bar']
        ];
        $httpParams = [
            ':custom' => 'custom-param',
            'query_parameters' => ['foo' => 'bar']
        ];

        $expectedHttpRequest = [
            'route' => '/my-index/my-collection/my-id/custom-param/_foobar',
            'method' => 'POST',
            'request' => [
                'controller' => $request['controller'],
                'action' => $request['action'],
                'collection' => $request['collection'],
                'index' => $request['index'],
                '_id' => $request['_id'],
                'body' => $request['body']
            ],
            'query_parameters' => ['foo' => 'bar']
        ];

        $kuzzle = new \Kuzzle\Kuzzle(self::FAKE_KUZZLE_HOST);

        $reflection = new \ReflectionClass(get_class($kuzzle));
        $convertRestRequest = $reflection->getMethod('convertRestRequest');
        $convertRestRequest->setAccessible(true);

        $httpRequest = $convertRestRequest->invokeArgs($kuzzle, [$request, $httpParams]);

        $this->assertEquals($expectedHttpRequest, $httpRequest);
    }

    public function testConvertRestRequestWithBadController()
    {
        $request = [
            'controller' => 'foo'
        ];
        $httpParams = [];

        $kuzzle = new \Kuzzle\Kuzzle(self::FAKE_KUZZLE_HOST);

        $reflection = new \ReflectionClass(get_class($kuzzle));
        $convertRestRequest = $reflection->getMethod('convertRestRequest');
        $convertRestRequest->setAccessible(true);

        try {
            $convertRestRequest->invokeArgs($kuzzle, [$request, $httpParams]);

            $this->fail("KuzzleTest::testConvertRestRequestWithBadController => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Unable to retrieve http route: controller "foo" information not found', $e->getMessage());
        }
    }

    public function testConvertRestRequestWithRouteAndBadController()
    {
        $request = [
            'route' => '/foo/bar',
            'controller' => 'foo'
        ];
        $httpParams = [];

        $kuzzle = new \Kuzzle\Kuzzle(self::FAKE_KUZZLE_HOST);

        $reflection = new \ReflectionClass(get_class($kuzzle));
        $convertRestRequest = $reflection->getMethod('convertRestRequest');
        $convertRestRequest->setAccessible(true);

        try {
            $convertRestRequest->invokeArgs($kuzzle, [$request, $httpParams]);

            $this->fail("KuzzleTest::testConvertRestRequestWithRouteAndBadController => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Unable to retrieve http method: controller "foo" information not found', $e->getMessage());
        }
    }

    public function testConvertRestRequestWithBadAction()
    {
        $request = [
            'controller' => 'document',
            'action' => 'foo'
        ];
        $httpParams = [];

        $kuzzle = new \Kuzzle\Kuzzle(self::FAKE_KUZZLE_HOST);

        $reflection = new \ReflectionClass(get_class($kuzzle));
        $convertRestRequest = $reflection->getMethod('convertRestRequest');
        $convertRestRequest->setAccessible(true);

        try {
            $convertRestRequest->invokeArgs($kuzzle, [$request, $httpParams]);

            $this->fail("KuzzleTest::testConvertRestRequestWithBadController => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Unable to retrieve http route: action "document:foo" information not found', $e->getMessage());
        }
    }

    public function testConvertRestRequestWithRouteAndBadAction()
    {
        $request = [
            'route' => '/foo/bar',
            'controller' => 'document',
            'action' => 'foo'
        ];
        $httpParams = [];

        $kuzzle = new \Kuzzle\Kuzzle(self::FAKE_KUZZLE_HOST);

        $reflection = new \ReflectionClass(get_class($kuzzle));
        $convertRestRequest = $reflection->getMethod('convertRestRequest');
        $convertRestRequest->setAccessible(true);

        try {
            $convertRestRequest->invokeArgs($kuzzle, [$request, $httpParams]);

            $this->fail("KuzzleTest::testConvertRestRequestWithRouteAndBadAction => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Unable to retrieve http method: action "document:foo" information not found', $e->getMessage());
        }
    }

    public function testQuery()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $token = uniqid();

        $httpRequest = [
            'route' => '/my-foo',
            'method' => 'POST',
            'request' => [
                'volatile' => [
                    'foo' => 'bar',
                    'bar' => 'baz'
                ],
                'action' => '',
                'controller' => '',
                'requestId' => $requestId,
                'headers' => [
                    'authorization' => 'Bearer ' . $token
                ]
            ],
            'query_parameters' => [
                'refresh' => 'foo'
            ]
        ];
        $httpResponse = [];

        $queryArgs = [
            'route' => '/:foo',
            'method' => 'post'
        ];
        $query = [
            'volatile' => [
                'bar' => 'baz'
            ]
        ];
        $options = [
            'refresh' => 'foo',
            'volatile' => [
                'foo' => 'bar'
            ],
            'httpParams' => [
                ':foo' => 'my-foo'
            ],
            'requestId' => $requestId
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        try {
            $kuzzle->setJwtToken($token);
            $kuzzle->query($queryArgs, $query, $options);
        }
        catch (Exception $e) {
            $this->fail('KuzzleTest::testQuery => Should not raise an exception');
        }
    }

    public function testQueryAuthCheckToken()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $token = uniqid();

        $httpRequest = [
            'route' => '/_checkToken',
            'method' => 'POST',
            'request' => [
                'action' => 'checkToken',
                'controller' => 'auth',
                'requestId' => $requestId,
                'volatile' => []
            ],
            'query_parameters' => []
        ];
        $httpResponse = [];

        $queryArgs = [
            'controller' => 'auth',
            'action' => 'checkToken',
        ];
        $query = [];
        $options = [
            'requestId' => $requestId
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        try {
            $kuzzle->setJwtToken($token);
            $kuzzle->query($queryArgs, $query, $options);
        }
        catch (Exception $e) {
            $this->fail('KuzzleTest::testQueryAuthCheckToken => Should not raise an exception');
        }
    }

    public function testQueryWithoutRouteNorController()
    {
        $url = self::FAKE_KUZZLE_HOST;

        $queryArgs = [];
        $query = [];
        $options = [];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        try {
            $kuzzle->query($queryArgs, $query, $options);

            $this->fail('KuzzleTest::testQueryWithoutRouteNorController => Should raise an exception');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Unable to execute query: missing controller or route/method', $e->getMessage());
        }
    }

    public function testQueryWithoutRouteNorAction()
    {
        $url = self::FAKE_KUZZLE_HOST;

        $queryArgs = [
            'controller' => 'foo'
        ];
        $query = [];
        $options = [];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        try {
            $kuzzle->query($queryArgs, $query, $options);

            $this->fail('KuzzleTest::testQueryWithoutRouteNorAction => Should raise an exception');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Unable to execute query: missing action or route/method', $e->getMessage());
        }
    }

    public function testQueryWithEmptyBody()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $queryArgs = [
            'route' => '/my-foo',
            'method' => 'POST',
            'query_parameters' => []
        ];
        $query = [
          'body' => []
        ];
        $options = [
            'volatile' => [],
            'requestId' => $requestId
        ];

        $httpRequest = [
            'route' => '/my-foo',
            'method' => 'POST',
            'request' => [
              'body' => (object)[],
              'volatile' => [],
              'controller' => '',
              'action' => '',
              'requestId' => $requestId
            ],
            'query_parameters' => []
        ];
        $httpResponse = [];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        try {
            $kuzzle->query($queryArgs, $query, $options);
        }
        catch (Exception $e) {
            $this->fail('KuzzleTest::testQuery => Should not raise an exception');
        }
    }

    public function testAddListener()
    {
        $url = self::FAKE_KUZZLE_HOST;

        $event = 'foo';
        $listener = function() {};

        $kuzzle = new \Kuzzle\Kuzzle($url);

        $kuzzle->addListener($event, $listener);

        $this->assertAttributeEquals([$event => [spl_object_hash($listener) => $listener]], 'listeners', $kuzzle);
    }

    public function testAddListenerWithBadListener()
    {
        $url = self::FAKE_KUZZLE_HOST;

        $event = 'foo';
        $listener = null;

        $kuzzle = new \Kuzzle\Kuzzle($url);

        try {
            $kuzzle->addListener($event, $listener);
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Unable to add a listener on event "' . $event . '": given listener is not callable', $e->getMessage());
        }
    }

    public function testRemoveListener()
    {
        $url = self::FAKE_KUZZLE_HOST;

        $event = 'foo';
        $listener1 = function() {};
        $listener2 = function() {};

        $kuzzle = new \Kuzzle\Kuzzle($url);

        $kuzzle->addListener($event, $listener1);
        $kuzzle->addListener($event, $listener2);
        $kuzzle->removeListener($event, $listener1);

        $this->assertAttributeEquals([$event => [spl_object_hash($listener2) => $listener2]], 'listeners', $kuzzle);
    }

    public function testRemoveAllListenersForOneEvent()
    {
        $url = self::FAKE_KUZZLE_HOST;

        $listener1 = function() {};
        $listener2 = function() {};
        $listener3 = function() {};
        $listener4 = function() {};

        $kuzzle = new \Kuzzle\Kuzzle($url);

        $kuzzle->addListener('foo', $listener1);
        $kuzzle->addListener('foo', $listener2);
        $kuzzle->addListener('bar', $listener3);
        $kuzzle->addListener('bar', $listener4);
        $kuzzle->removeAllListeners('foo');

        $this->assertAttributeEquals(['bar' => [spl_object_hash($listener3) => $listener3, spl_object_hash($listener4) => $listener4]], 'listeners', $kuzzle);
    }

    public function testRemoveAllListeners()
    {
        $url = self::FAKE_KUZZLE_HOST;

        $listener = function() {};

        $kuzzle = new \Kuzzle\Kuzzle($url);

        $kuzzle->addListener('foo', $listener);
        $kuzzle->addListener('bar', $listener);
        $kuzzle->removeAllListeners();

        $this->assertAttributeEquals([], 'listeners', $kuzzle);
    }

    public function testEmitEvent()
    {
        $url = self::FAKE_KUZZLE_HOST;

        $event = 'foo';
        $triggerCount = 0;
        $triggerArgs = [];

        $listener1 = function() use (&$triggerArgs, &$triggerCount){
          $triggerArgs[$triggerCount++] = func_get_args();
        };

        $kuzzle = new \Kuzzle\Kuzzle($url);

        $kuzzle->addListener($event, $listener1);

        $kuzzle->emitEvent($event, 'foo', 'bar');
        $this->assertEquals($triggerCount, 1);
        $this->assertEquals(['foo', 'bar'], $triggerArgs[0]);

        $listener2 = Closure::bind($listener1, null);
        $kuzzle->addListener($event, $listener2);

        $triggerCount = 0;
        $triggerArgs = [];

        $kuzzle->emitEvent($event, 'foo', 'bar');
        $this->assertEquals($triggerCount, 2);
        $this->assertEquals(['foo', 'bar'], $triggerArgs[1]);
    }

    public function testAddHeaders()
    {
        /**
         * @todo
         */
    }

    public function testSetHeaders()
    {
        /**
         * @todo
         */
    }
}
