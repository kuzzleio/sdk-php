<?php

use Kuzzle\Util\CurlRequest;

class AuthTest extends \PHPUnit_Framework_TestCase
{
    const FAKE_KUZZLE_HOST = '127.0.0.1';
    const FAKE_KUZZLE_URL = 'http://127.0.0.1:7512';

    public function testCheckToken()
    {
        $fakeToken = uniqid();
        $url = self::FAKE_KUZZLE_HOST;

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
            'route' => '/_checkToken',
            'request' => [
                'action' => 'checkToken',
                'controller' => 'auth',
                'volatile' => [],
                'body' => [
                    'token' => $fakeToken
                ],
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => []
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
        $response = $kuzzle->auth->checkToken($fakeToken, $options);

        $this->assertEquals($checkTokenResponse, $response);
    }

    public function testCheckTokenWithoutToken() {
        $url = self::FAKE_KUZZLE_HOST;
        $kuzzle = new \Kuzzle\Kuzzle($url);

        try {
            $kuzzle->auth->checkToken('');

            $this->fail("KuzzleTest::testCheckTokenWithoutToken => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testGetMyRights()
    {
        $url = self::FAKE_KUZZLE_HOST;

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
            'route' => '/users/_me/_rights',
            'request' => [
                'action' => 'getMyRights',
                'controller' => 'auth',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
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

        $response = $kuzzle->auth->getMyRights($options);

        $this->assertEquals($getMyRightsResponse['hits'], $response);
    }

    public function testLogin()
    {
        $url = self::FAKE_KUZZLE_HOST;
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
            'route' => '/_login/' . $strategy,
            'request' => [
                'action' => 'login',
                'controller' => 'auth',
                'volatile' => [],
                'body' => json_encode($credentials),
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => [
              'expiresIn' => $expiresIn
            ]
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
        $response = $kuzzle->auth->login($strategy, $credentials, $expiresIn, $options);

        $this->assertEquals($loginResponse, $response);
        $this->assertAttributeEquals($loginResponse['jwt'], 'jwtToken', $kuzzle);
    }

    public function testLoginWithoutStrategy() {
        $url = self::FAKE_KUZZLE_HOST;
        $kuzzle = new \Kuzzle\Kuzzle($url);

        try {
            $kuzzle->auth->login('');

            $this->fail("KuzzleTest::testLoginWithoutStrategy => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testLoginWithoutCredentials() {
        $url = self::FAKE_KUZZLE_HOST;
        $kuzzle = new \Kuzzle\Kuzzle($url);

        try {
            $kuzzle->auth->login('local', []);

            $this->fail("KuzzleTest::testLoginWithoutCredentials => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testLogout()
    {
        $url = self::FAKE_KUZZLE_HOST;

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
            'route' => '/_logout',
            'request' => [
                'action' => 'logout',
                'controller' => 'auth',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => []
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
        $response = $kuzzle->auth->logout($options);

        $this->assertEquals($logoutResponse, $response);
        $this->assertAttributeEquals(null, 'jwtToken', $kuzzle);
    }

    public function testUpdateSelf()
    {
        $url = self::FAKE_KUZZLE_HOST;
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
            'route' => '/_updateSelf',
            'request' => [
                'action' => 'updateSelf',
                'controller' => 'auth',
                'volatile' => [],
                'body' => $content,
                'requestId' => $options['requestId']
            ],
            'method' => 'PUT',
            'query_parameters' => []
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

        $response = $kuzzle->auth->updateSelf($content, $options);

        $this->assertEquals($updateSelfResponse, $response);
    }

    public function testGetCurrentUser()
    {
        $url = self::FAKE_KUZZLE_HOST;

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
            'route' => '/users/_me',
            'request' => [
                'action' => 'getCurrentUser',
                'controller' => 'auth',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        // mock response
        $whoAmIResponse = [
            '_id' => 'alovelace',
            '_source' => [
                'profileIds' => ['admin'],
                'foo' => 'bar'
            ],
            '_meta' => []
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

        $response = $kuzzle->auth->getCurrentUser($options);

        $this->assertEquals($whoAmIResponse['_id'], $response['_id']);
        $this->assertEquals($whoAmIResponse['_source'], $response['_source']);
    }

    public function testGetStrategies()
    {
        $url = self::FAKE_KUZZLE_HOST;

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
            'route' => '/strategies',
            'request' => [
                'action' => 'getStrategies',
                'controller' => 'auth',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        // mock response
        $whoAmIResponse = [
            'local',
            'facebook'
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

        $response = $kuzzle->auth->getStrategies($options);

        $this->assertEquals($whoAmIResponse[0], $response[0]);
        $this->assertEquals($whoAmIResponse[1], $response[1]);
    }

    public function testCreateMyCredentials()
    {
        $url = self::FAKE_KUZZLE_HOST;

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
            'route' => '/credentials/local/_me/_create',
            'request' => [
                'action' => 'createMyCredentials',
                'controller' => 'auth',
                'volatile' => [],
                'requestId' => $options['requestId'],
                'body' => json_encode(['foo' => 'bar'])
            ],
            'method' => 'POST',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => [
                "username" => "foo",
                "kuid" => "42"
            ]
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $kuzzle->auth->createMyCredentials("local", ["foo"=>"bar"], $options);
    }

    public function testCreateMyCredentialsWithoutStrategy() {
        $url = self::FAKE_KUZZLE_HOST;
        $kuzzle = new \Kuzzle\Kuzzle($url);

        try {
            $kuzzle->auth->createMyCredentials('', ['username' => 'HitchHicker']);

            $this->fail("KuzzleTest::testCreateMyCredentialsWithoutStrategy => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testCreateMyCredentialsWithoutCredentials() {
        $url = self::FAKE_KUZZLE_HOST;
        $kuzzle = new \Kuzzle\Kuzzle($url);

        try {
            $kuzzle->auth->createMyCredentials('local', []);

            $this->fail("KuzzleTest::testCreateMyCredentialsWithoutCredentials => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testDeleteMyCredentials()
    {
        $url = self::FAKE_KUZZLE_HOST;

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
            'route' => '/credentials/local/_me',
            'request' => [
                'action' => 'deleteMyCredentials',
                'controller' => 'auth',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'DELETE',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => [
                "acknowledged" => true
            ]
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $kuzzle->auth->deleteMyCredentials("local", $options);
    }

    public function testDeleteMyCredentialsWithoutStrategy() {
        $url = self::FAKE_KUZZLE_HOST;
        $kuzzle = new \Kuzzle\Kuzzle($url);

        try {
            $kuzzle->auth->deleteMyCredentials('');

            $this->fail("KuzzleTest::testDeleteMyCredentialsWithoutStrategy => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testCredentialsExist()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $strategy = "local";
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
            'route' => '/credentials/' . $strategy . '/_me/_exists',
            'request' => [
                'action' => 'credentialsExist',
                'controller' => 'auth',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => true
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $kuzzle->auth->credentialsExist($strategy, $options);
    }

    public function testCredentialsExistWithoutStrategy() {
        $url = self::FAKE_KUZZLE_HOST;
        $kuzzle = new \Kuzzle\Kuzzle($url);

        try {
            $kuzzle->auth->credentialsExist('');

            $this->fail("KuzzleTest::testcredentialsExistWithoutStrategy => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testGetMyCredentials()
    {
        $url = self::FAKE_KUZZLE_HOST;

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
            'route' => '/credentials/local/_me',
            'request' => [
                'action' => 'getMyCredentials',
                'controller' => 'auth',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => [
                "username" => "foo",
                "kuid" => "42"
            ]
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $kuzzle->auth->getMyCredentials("local", $options);
    }

    public function testGetMyCredentialsWithoutStrategy() {
        $url = self::FAKE_KUZZLE_HOST;
        $kuzzle = new \Kuzzle\Kuzzle($url);

        try {
            $kuzzle->auth->getMyCredentials('');

            $this->fail("KuzzleTest::testGetMyCredentialsWithoutStrategy => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testUpdateMyCredentials()
    {
        $url = self::FAKE_KUZZLE_HOST;

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
            'route' => '/credentials/local/_me/_update',
            'request' => [
                'action' => 'updateMyCredentials',
                'controller' => 'auth',
                'volatile' => [],
                'requestId' => $options['requestId'],
                'body' => json_encode([
                    'foo' => 'bar'
                ])
            ],
            'method' => 'PUT',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => [
                "username" => "foo",
                "kuid" => "42"
            ]
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $kuzzle->auth->updateMyCredentials("local", ['foo' => 'bar'], $options);
    }

    public function testUpdateMyCredentialsWithoutStrategy() {
        $url = self::FAKE_KUZZLE_HOST;
        $kuzzle = new \Kuzzle\Kuzzle($url);

        try {
            $kuzzle->auth->updateMyCredentials('', ['username' => 'HitchHicker']);

            $this->fail("KuzzleTest::testUpdateMyCredentialsWithoutStrategy => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testUpdateMyCredentialsWithoutCredentials() {
        $url = self::FAKE_KUZZLE_HOST;
        $kuzzle = new \Kuzzle\Kuzzle($url);

        try {
            $kuzzle->auth->updateMyCredentials('local', []);

            $this->fail("KuzzleTest::testUpdateMyCredentialsWithoutCredentials => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testValidateMyCredentials()
    {
        $url = self::FAKE_KUZZLE_HOST;

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
            'route' => '/credentials/local/_me/_validate',
            'request' => [
                'action' => 'validateMyCredentials',
                'controller' => 'auth',
                'volatile' => [],
                'requestId' => $options['requestId'],
                'body' => json_encode([
                    'foo' => 'bar'
                ])
            ],
            'method' => 'POST',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => true
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $kuzzle->auth->validateMyCredentials("local", ['foo' => 'bar'], $options);
    }

    public function testValidateMyCredentialsWithoutStrategy() {
        $url = self::FAKE_KUZZLE_HOST;
        $kuzzle = new \Kuzzle\Kuzzle($url);

        try {
            $kuzzle->auth->validateMyCredentials('', ['username' => 'HitchHicker']);

            $this->fail("KuzzleTest::testValidateMyCredentialsWithoutStrategy => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testValidateMyCredentialsWithoutCredentials() {
        $url = self::FAKE_KUZZLE_HOST;
        $kuzzle = new \Kuzzle\Kuzzle($url);

        try {
            $kuzzle->auth->validateMyCredentials('local', []);

            $this->fail("KuzzleTest::testValidateMyCredentialsWithoutCredentials => Should raise an exception");
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }
}
