<?php

class KuzzleTest extends \PHPUnit_Framework_TestCase
{
    const FAKE_KUZZLE_URL = 'http://127.0.0.1:7511';
    
    public function testSimpleConstructor()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_URL;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);

            // Assert type
            $this->assertInstanceOf('\Kuzzle\Kuzzle', $kuzzle);

            // Assert Url
            $this->assertAttributeEquals($url, 'url', $kuzzle);

            // Assert if one (random) route is loaded
            $routesDescription = $this->readAttribute($kuzzle, 'routesDescription');
            $routesNow = [
                'method' => 'get',
                'name' => 'now',
                'route' =>  '/api/1.0/_now'
            ];

            $this->assertEquals($routesNow, $routesDescription['read']['now']);
        }
        catch (Exception $e) {
            $this->fail('KuzzleTest::testSimpleConstructor => Should not raise an exception (base configuration file should be valid)');
        }
    }

    public function testDataCollectionFactory()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_URL;
        $index = 'index';
        $collection = 'collection';

        try {

            $kuzzle = new \Kuzzle\Kuzzle($url);
            $dataCollection = $kuzzle->dataCollectionFactory($collection, $index);

            // Assert type
            $this->assertInstanceOf('\Kuzzle\DataCollection', $dataCollection);

            // Assert Properties
            $this->assertAttributeEquals($kuzzle, 'kuzzle', $dataCollection);
            $this->assertAttributeEquals($index, 'index', $dataCollection);
            $this->assertAttributeEquals($collection, 'collection', $dataCollection);
        }
        catch (Exception $e) {
            $this->fail('KuzzleTest::testDataCollectionFactory => Should not raise an exception');
        }
    }

    public function testMemoryStorage()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_URL;

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

    public function testSetDefaultIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_URL;
        $index = 'index';

        $kuzzle = new \Kuzzle\Kuzzle($url);
        $kuzzle->setDefaultIndex($index);

        // Assert Properties
        $this->assertAttributeEquals($index, 'defaultIndex', $kuzzle);
    }

    public function testSetJwtToken()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_URL;
        $token = uniqid();

        $kuzzle = new \Kuzzle\Kuzzle($url);
        $kuzzle->setJwtToken($token);

        // Assert Properties
        $this->assertAttributeEquals($token, 'jwtToken', $kuzzle);
    }

    public function testDataCollectionFactoryDefaultIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_URL;
        $index = 'index';
        $collection = 'collection';

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url, ['defaultIndex' => $index]);
            $dataCollection = $kuzzle->dataCollectionFactory($collection);

            // Assert type
            $this->assertInstanceOf('\Kuzzle\DataCollection', $dataCollection);

            // Assert Properties
            $this->assertAttributeEquals($index, 'index', $dataCollection);
            $this->assertAttributeEquals($collection, 'collection', $dataCollection);
        }
        catch (Exception $e) {
            $this->fail('KuzzleTest::testDataCollectionFactory => Should not raise an exception');
        }
    }

    public function testDataCollectionFactoryWithoutIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_URL;
        $collection = 'collection';

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->dataCollectionFactory($collection);

            $this->fail('KuzzleTest::testDataCollectionFactory => Should raise an exception (dataCollectionFactory could not be called without index nor default index)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Unable to create a new data collection object: no index specified', $e->getMessage());
        }
    }

    public function testRouteDescriptionWithoutConfigurationFile()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_URL;
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
        $url = self::FAKE_KUZZLE_URL;
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

    public function testCheckToken()
    {
        $fakeToken = uniqid();
        $url = self::FAKE_KUZZLE_URL;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/_checkToken',
            'request' => [
                'action' => 'checkToken',
                'controller' => 'auth',
                'metadata' => [],
                'body' => [
                    'token' => $fakeToken
                ],
                'requestId' => $options['requestId']
            ],
            'method' => 'POST'
        ];

        // mock response
        $checkTokenResponse = ['now' => time()];
        $httpResponse = [
            'error' => null,
            'result' => $checkTokenResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->checkToken($fakeToken, $options);

        $this->assertEquals($checkTokenResponse, $response);
    }

    public function testGetAllStatistics()
    {
        $url = self::FAKE_KUZZLE_URL;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/_getAllStats',
            'request' => [
                'action' => 'getAllStats',
                'controller' => 'admin',
                'metadata' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET'
        ];

        // mock response
        $getAllStatisticsResponse = ['hits' => []];
        $httpResponse = [
            'error' => null,
            'result' => $getAllStatisticsResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->getAllStatistics($options);

        $this->assertEquals($getAllStatisticsResponse['hits'], $response);
    }

    public function testGetMyRights()
    {
        $url = self::FAKE_KUZZLE_URL;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/users/_me/_rights',
            'request' => [
                'action' => 'getMyRights',
                'controller' => 'auth',
                'metadata' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET'
        ];

        // mock response
        $getMyRightsResponse = ['hits' => []];
        $httpResponse = [
            'error' => null,
            'result' => $getMyRightsResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->getMyRights($options);

        $this->assertEquals($getMyRightsResponse['hits'], $response);
    }

    public function testGetServerInfo()
    {
        /**
         * @todo: missing server info http route in kuzzle
         */
        /*$url = self::FAKE_KUZZLE_URL;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/_serverInfo',
            'request' => [
                'action' => 'serverInfo',
                'controller' => 'read',
                'metadata' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET'
        ];

        // mock response
        $getServerInfoResponse = ['serverInfo' => []];
        $httpResponse = [
            'error' => null,
            'result' => $getServerInfoResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $response = $kuzzle->getServerInfo($options);

        $this->assertEquals($getServerInfoResponse['serverInfo'], $response);*/
    }

    public function testGetLastStatistics()
    {
        $url = self::FAKE_KUZZLE_URL;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/_getLastStats',
            'request' => [
                'action' => 'getLastStats',
                'controller' => 'admin',
                'metadata' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET'
        ];

        // mock response
        $getLastStatsResponse = [];
        $httpResponse = [
            'error' => null,
            'result' => $getLastStatsResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->getStatistics('', $options);

        $this->assertEquals([$getLastStatsResponse], $response);
    }

    public function testGetStatistics()
    {
        $url = self::FAKE_KUZZLE_URL;
        $statsTime = time();

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/_getStats',
            'request' => [
                'action' => 'getStats',
                'controller' => 'admin',
                'metadata' => [],
                'body' => [
                    'startTime' => $statsTime
                ],
                'requestId' => $options['requestId']
            ],
            'method' => 'POST'
        ];

        // mock response
        $getStatsResponse = ['hits' => []];
        $httpResponse = [
            'error' => null,
            'result' => $getStatsResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->getStatistics($statsTime, $options);

        $this->assertEquals($getStatsResponse['hits'], $response);
    }

    public function testListAllCollections()
    {
        $url = self::FAKE_KUZZLE_URL;
        $index = 'index';
        $collectionType = 'all';

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/' . $index . '/_listCollections/' . $collectionType,
            'request' => [
                'index' => $index,
                'action' => 'listCollections',
                'controller' => 'read',
                'metadata' => [],
                'body' => [
                    'type' => $collectionType
                ],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET'
        ];

        // mock response
        $listCollectionsResponse = ['collections' => []];
        $httpResponse = [
            'error' => null,
            'result' => $listCollectionsResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->listCollections($index, $options);

        $this->assertEquals($listCollectionsResponse['collections'], $response);
    }

    public function testListRealtimeCollections()
    {
        $url = self::FAKE_KUZZLE_URL;
        $index = 'index';
        $collectionType = 'realtime';

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'type' => $collectionType,
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/' . $index . '/_listCollections/' . $collectionType,
            'request' => [
                'index' => $index,
                'action' => 'listCollections',
                'controller' => 'read',
                'metadata' => [],
                'body' => [
                    'type' => $collectionType
                ],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET'
        ];

        // mock response
        $listCollectionsResponse = ['collections' => []];
        $httpResponse = [
            'error' => null,
            'result' => $listCollectionsResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->listCollections($index, $options);

        $this->assertEquals($listCollectionsResponse['collections'], $response);
    }

    public function testListCollectionsWithoutIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_URL;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->listCollections();

            $this->fail('KuzzleTest::testListCollectionsWithoutIndex => Should raise an exception (could not be called without index nor default index)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Unable to list collections: no index specified', $e->getMessage());
        }
    }

    public function testListIndexes()
    {
        $url = self::FAKE_KUZZLE_URL;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/_listIndexes',
            'request' => [
                'action' => 'listIndexes',
                'controller' => 'read',
                'metadata' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET'
        ];

        // mock response
        $listIndexesResponse = ['indexes' => []];
        $httpResponse = [
            'error' => null,
            'result' => $listIndexesResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->listIndexes($options);

        $this->assertEquals($listIndexesResponse['indexes'], $response);
    }

    public function testLogin()
    {
        $url = self::FAKE_KUZZLE_URL;
        $strategy = 'local';
        $expiresIn = '1h';
        $credentials = [
            'username' => 'foo',
            'password' => 'bar'
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/_login/' . $strategy,
            'request' => [
                'action' => 'login',
                'controller' => 'auth',
                'metadata' => [],
                'body' => [
                    'username' => $credentials['username'],
                    'password' => $credentials['password'],
                    'expiresIn' => $expiresIn,
                ],
                'requestId' => $options['requestId']
            ],
            'method' => 'POST'
        ];

        // mock response
        $loginResponse = ['jwt' => uniqid()];
        $httpResponse = [
            'error' => null,
            'result' => $loginResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->login($strategy, $credentials, $expiresIn, $options);

        $this->assertEquals($loginResponse, $response);
        $this->assertAttributeEquals($loginResponse['jwt'], 'jwtToken', $kuzzle);
    }

    public function testLogout()
    {
        $url = self::FAKE_KUZZLE_URL;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/_logout',
            'request' => [
                'action' => 'logout',
                'controller' => 'auth',
                'metadata' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET'
        ];

        // mock response
        $logoutResponse = [];
        $httpResponse = [
            'error' => null,
            'result' => $logoutResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->logout($options);

        $this->assertEquals($logoutResponse, $response);
        $this->assertAttributeEquals(null, 'jwtToken', $kuzzle);
    }

    public function testNow()
    {
        $url = self::FAKE_KUZZLE_URL;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/_now',
            'request' => [
                'action' => 'now',
                'controller' => 'read',
                'metadata' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET'
        ];

        // mock response (timestamp is given in milliseconds)
        $logoutResponse = ['now' => (time()) * 1000];
        $httpResponse = [
            'error' => null,
            'result' => $logoutResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->now($options);

        $this->assertInstanceOf('DateTime', $response);
        $this->assertEquals($logoutResponse['now'] / 1000, $response->getTimestamp());
    }

    public function testRefreshIndex()
    {
        $url = self::FAKE_KUZZLE_URL;
        $index = 'index';

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/' . $index . '/_refresh',
            'request' => [
                'index' => $index,
                'action' => 'refreshIndex',
                'controller' => 'admin',
                'metadata' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'POST'
        ];

        // mock response
        $refreshIndexResponse = [];
        $httpResponse = [
            'error' => null,
            'result' => $refreshIndexResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->refreshIndex($index, $options);

        $this->assertEquals($refreshIndexResponse, $response);
    }

    public function testRefreshIndexWithoutIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_URL;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->refreshIndex();

            $this->fail('KuzzleTest::testRefreshIndexWithoutIndex => Should raise an exception (could not be called without index nor default index)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Unable to refresh index: no index specified', $e->getMessage());
        }
    }

    public function testSetAutoRefresh()
    {
        $url = self::FAKE_KUZZLE_URL;
        $index = 'index';
        $autoRefresh = true;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/' . $index . '/_autoRefresh',
            'request' => [
                'index' => $index,
                'action' => 'setAutoRefresh',
                'controller' => 'admin',
                'metadata' => [],
                'body' => [
                    'autoRefresh' => $autoRefresh
                ],
                'requestId' => $options['requestId']
            ],
            'method' => 'POST'
        ];

        // mock response
        $setAutoRefreshResponse = [];
        $httpResponse = [
            'error' => null,
            'result' => $setAutoRefreshResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->setAutoRefresh($index, $autoRefresh, $options);

        $this->assertEquals($setAutoRefreshResponse, $response);
    }

    public function testSetAutoRefreshWithoutIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_URL;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->setAutoRefresh();

            $this->fail('KuzzleTest::testSetAutoRefreshWithoutIndex => Should raise an exception (could not be called without index nor default index)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Unable to set auto refresh on index: no index specified', $e->getMessage());
        }
    }

    public function testUpdateSelf()
    {
        $url = self::FAKE_KUZZLE_URL;
        $content = ['foo' => 'bar'];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/_updateSelf',
            'request' => [
                'action' => 'updateSelf',
                'controller' => 'auth',
                'metadata' => [],
                'body' => $content,
                'requestId' => $options['requestId']
            ],
            'method' => 'PUT'
        ];

        // mock response
        $updateSelfResponse = [];
        $httpResponse = [
            'error' => null,
            'result' => $updateSelfResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->updateSelf($content, $options);

        $this->assertEquals($updateSelfResponse, $response);
    }

    public function testWhoAmI()
    {
        $url = self::FAKE_KUZZLE_URL;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/api/1.0/users/_me',
            'request' => [
                'action' => 'getCurrentUser',
                'controller' => 'auth',
                'metadata' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET'
        ];

        // mock response
        $whoAmIResponse = [
            '_id' => 'alovelace',
            '_source' => [
                'profile' => 'admin',
                'foo' => 'bar'
            ]
        ];
        $httpResponse = [
            'error' => null,
            'result' => $whoAmIResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->whoAmI($options);

        $this->assertInstanceOf('\Kuzzle\Security\User', $response);
        $this->assertAttributeEquals($whoAmIResponse['_id'], 'id', $response);
        $this->assertAttributeEquals($whoAmIResponse['_source'], 'content', $response);
    }
}