<?php

use Kuzzle\Kuzzle;

class SecurityTest extends \PHPUnit_Framework_TestCase
{
    const FAKE_KUZZLE_HOST = '127.0.0.1';
    const FAKE_KUZZLE_URL = 'http://127.0.0.1:7512';

    function testCreateProfile()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $profileId = uniqid();
        $policies = [
            'policies' => [
                'roleId' => 'default',
                'restrictedTo' => []
            ]
        ];

        $httpRequest = [
            'route' => '/profiles/' . $profileId . '/_create',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'createProfile',
                'requestId' => $requestId,
                '_id' => $profileId,
                'body' => json_encode($policies)
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            '_id' => $profileId,
            '_source' => $policies,
            '_meta' => [],
            '_version' => 1
        ];
        $httpResponse = [
            'error' => null,
            'result' => $saveResponse
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

        $result = $kuzzle->security->createProfile($profileId, $policies, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Profile', $result);
        $this->assertAttributeEquals($profileId, '_id', $result);
        $this->assertAttributeEquals($policies['policies'], 'policies', $result);
    }

    public function testCreateProfileWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createProfile('', [ 'policies' => [ 'roleId' => 'default', 'restrictedTo' => [] ] ]);

            $this->fail('KuzzleTest::testCreateProfileWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createProfile: Unable to create profile: no id or body specified', $e->getMessage());
        }
    }

    public function testCreateProfileWithoutBody()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createProfile('profile', []);

            $this->fail('KuzzleTest::testCreateProfileWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createProfile: Unable to create profile: no id or body specified', $e->getMessage());
        }
    }

    public function testCreateProfileWithMalformedBody()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createProfile('profile', [ 'roleId' => 'default', 'restrictedTo' => [] ]);

            $this->fail('KuzzleTest::testCreateProfileWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createProfile: Unable to create given profile: body["policies"] property is required', $e->getMessage());
        }
    }

    function testCreateOrReplaceProfile()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $profileId = uniqid();
        $policies = [
            'policies' => [
                'roleId' => 'default',
                'restrictedTo' => []
            ]
        ];

        $httpRequest = [
            'route' => '/profiles/' . $profileId,
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'createOrReplaceProfile',
                'requestId' => $requestId,
                '_id' => $profileId,
                'body' => json_encode($policies)
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            '_id' => $profileId,
            '_source' => $policies,
            '_meta' => [],
            '_version' => 1
        ];
        $httpResponse = [
            'error' => null,
            'result' => $saveResponse
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

        $result = $kuzzle->security->createOrReplaceProfile($profileId, $policies, [
            'replaceIfExist' => true,
            'requestId' => $requestId
        ]);

        $this->assertInstanceOf('Kuzzle\Security\Profile', $result);
        $this->assertAttributeEquals($profileId, '_id', $result);
        $this->assertAttributeEquals($policies['policies'], 'policies', $result);
    }

    public function testCreateOrReplaceProfileWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createOrReplaceProfile('', [ 'policies' => [ 'roleId' => 'default', 'restrictedTo' => [] ] ]);

            $this->fail('KuzzleTest::testCreateOrReplaceProfileWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createOrReplaceProfile: Unable to create or replace profile: no id or body specified', $e->getMessage());
        }
    }

    public function testCreateOrReplaceProfileWithoutPolicies()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createOrReplaceProfile('profile', []);

            $this->fail('KuzzleTest::testCreateOrReplaceProfileWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createOrReplaceProfile: Unable to create or replace profile: no id or body specified', $e->getMessage());
        }
    }

    public function testCreateOrReplaceProfileWithMalformedPolicies()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createOrReplaceProfile('profile', [ 'roleId' => 'default', 'restrictedTo' => [] ]);

            $this->fail('KuzzleTest::testCreateOrReplaceProfileWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createOrReplaceProfile: Unable to create or replace given profile: body["policies"] property is required', $e->getMessage());
        }
    }

    function testCreateRole()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $roleId = uniqid();
        $roleContent = [
            'controllers' => [
                '*' => [
                    'actions'=> [
                        ['*' => true]
                    ]
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/roles/' . $roleId . '/_create',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'createRole',
                'requestId' => $requestId,
                '_id' => $roleId,
                'body' => json_encode($roleContent)
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            '_id' => $roleId,
            '_source' => $roleContent,
            '_meta' => [],
            '_version' => 1
        ];
        $httpResponse = [
            'error' => null,
            'result' => $saveResponse
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

        $result = $kuzzle->security->createRole($roleId, $roleContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Role', $result);
        $this->assertAttributeEquals($roleId, '_id', $result);
        $this->assertAttributeEquals($roleContent['controllers'], 'controllers', $result);
    }

    public function testCreateRoleWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createRole('', [ 'controllers' => [ '*' => [ 'actions'=> [ ['*' => true] ] ] ] ]);

            $this->fail('KuzzleTest::testCreateRoleWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createRole: Unable to create role: no id or body specified', $e->getMessage());
        }
    }

    public function testCreateRoleWithoutBody()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createRole('profile', []);

            $this->fail('KuzzleTest::testCreateRoleWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createRole: Unable to create role: no id or body specified', $e->getMessage());
        }
    }

    public function testCreateRoleWithMalformedBody()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createRole('profile', [ '*' => [ 'actions'=> [ ['*' => true] ] ] ]);

            $this->fail('KuzzleTest::testCreateRoleWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createRole: Unable to create given role: body["controllers"] property is required', $e->getMessage());
        }
    }

    function testCreateOrReplaceRole()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $roleId = uniqid();
        $roleContent = [
            'controllers' => [
                '*' => [
                    'actions'=> [
                        ['*' => true]
                    ]
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/roles/' . $roleId,
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'createOrReplaceRole',
                'requestId' => $requestId,
                '_id' => $roleId,
                'body' => json_encode($roleContent)
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            '_id' => $roleId,
            '_source' => $roleContent,
            '_meta' => [],
            '_version' => 1
        ];
        $httpResponse = [
            'error' => null,
            'result' => $saveResponse
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

        $result = $kuzzle->security->createOrReplaceRole($roleId, $roleContent, [
            'replaceIfExist' => true,
            'requestId' => $requestId
        ]);

        $this->assertInstanceOf('Kuzzle\Security\Role', $result);
        $this->assertAttributeEquals($roleId, '_id', $result);
        $this->assertAttributeEquals($roleContent['controllers'], 'controllers', $result);
    }

    public function testCreateOrReplaceRoleWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createOrReplaceRole('', [ 'controllers' => [ '*' => [ 'actions'=> [ ['*' => true] ] ] ] ]);

            $this->fail('KuzzleTest::testCreateOrReplaceRoleWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createOrReplaceRole: Unable to create or replace role: no id or body specified', $e->getMessage());
        }
    }

    public function testCreateOrReplaceRoleWithoutBody()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createOrReplaceRole('profile', []);

            $this->fail('KuzzleTest::testCreateOrReplaceRoleWithoutContent => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createOrReplaceRole: Unable to create or replace role: no id or body specified', $e->getMessage());
        }
    }

    public function testCreateOrReplaceRoleWithMalformedBody()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createOrReplaceRole('profile', [ '*' => [ 'actions'=> [ ['*' => true] ] ] ]);

            $this->fail('KuzzleTest::testCreateOrReplaceRoleWithoutContent => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createOrReplaceRole: Unable to create or replace given role: body["controllers"] property is required', $e->getMessage());
        }
    }

    function testCreateUser()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $userId = uniqid();
        $userContent = [
            'content' => [
                'profileIds' => ['admin']
            ],
            'credentials' => ['some' => 'credentials']
        ];

        $httpRequest = [
            'route' => '/users/' . $userId . '/_create',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'createUser',
                'requestId' => $requestId,
                '_id' => $userId,
                'body' => json_encode($userContent)
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            '_id' => $userId,
            '_source' => [
                'profileIds' => ['admin']
            ],
            '_meta' => [],
            '_version' => 1
        ];
        $httpResponse = [
            'error' => null,
            'result' => $saveResponse
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

        $result = $kuzzle->security->createUser($userId, $userContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\User', $result);
        $this->assertAttributeEquals($userId, '_id', $result);
    }

    public function testCreateUserWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createUser('', [ 'content' => [ 'profileIds' => ['admin'] ], 'credentials' => ['some' => 'credentials'] ]);

            $this->fail('KuzzleTest::testCreateUserWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createUser: Unable to create user: no id or body specified', $e->getMessage());
        }
    }

    public function testCreateUserWithoutBody()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createUser('user', []);

            $this->fail('KuzzleTest::testCreateUserWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createUser: Unable to create user: no id or body specified', $e->getMessage());
        }
    }

    public function testCreateUserWithMalformedBodyContent()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createUser('profile', [ 'credentials' => [ 'profileIds' => ['admin'] ] ]);

            $this->fail('KuzzleTest::testCreateUserWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createUser: Unable to create user: body["content"] is required', $e->getMessage());
        }
    }


    function testCreateRestrictedUser()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $userId = uniqid();
        $userContent = [
            'content' => [
                'profileIds' => ['admin']
            ],
            'credentials' => ['some' => 'credentials']
        ];

        $httpRequest = [
            'route' => '/users/' . $userId . '/_createRestricted',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'createRestrictedUser',
                'requestId' => $requestId,
                '_id' => $userId,
                'body' => json_encode($userContent)
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            '_id' => $userId,
            '_source' => [
                'profileIds' => ['admin']
            ],
            '_meta' => [],
            '_version' => 1
        ];
        $httpResponse = [
            'error' => null,
            'result' => $saveResponse
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

        $result = $kuzzle->security->createRestrictedUser($userId, $userContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\User', $result);
        $this->assertAttributeEquals($userId, '_id', $result);
    }

    public function testCreateRestrictedUserWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createRestrictedUser('', [['roleId' => 'default', 'restrictedTo' => []]]);

            $this->fail('KuzzleTest::testCreateRestrictedUserWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createRestrictedUser: Unable to create restricted user: no id or body specified', $e->getMessage());
        }
    }

    public function testCreateRestrictedUserWithoutContent()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createRestrictedUser('profile', []);

            $this->fail('KuzzleTest::testCreateRestrictedUserWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createRestrictedUser: Unable to create restricted user: no id or body specified', $e->getMessage());
        }
    }

    function testCreateFirstAdmin()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $reset = false;
        $userId = uniqid();
        $userContent = [
            'content' => [
                'name' => "The New Admin"
            ],
            'credentials' => ['some' => 'credentials']
        ];

        $httpRequest = [
            'route' => '/_createFirstAdmin',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'createFirstAdmin',
                'requestId' => $requestId,
                'reset' => $reset,
                'body' => json_encode($userContent)
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            '_id' => $userId,
            '_source' => [
                'profileIds' => ['admin']
            ],
            '_meta' => [],
            '_version' => 1
        ];
        $httpResponse = [
            'error' => null,
            'result' => $saveResponse
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

        $result = $kuzzle->security->createFirstAdmin($userContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\User', $result);
        $this->assertAttributeEquals($userId, '_id', $result);
        $this->assertAttributeEquals(['admin'], 'profileIds', $result);
    }

    function testReplaceUser()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $userId = uniqid();

        $userContent = [
            'profileIds' => ['admin']
        ];

        $httpRequest = [
            'route' => '/users/' . $userId . '/_replace',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'replaceUser',
                'requestId' => $requestId,
                '_id' => $userId,
                'body' => json_encode($userContent)
            ],
            'query_parameters' => [],
        ];
        $replaceResponse = [
            '_id' => $userId,
            '_source' => $userContent,
            '_meta' => [],
            '_version' => 1
        ];
        $httpResponse = [
            'error' => null,
            'result' => $replaceResponse
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

        $result = $kuzzle->security->replaceUser($userId, $userContent, ['requestId' => $requestId]);

        $this->assertEquals($userContent['profileIds'], $result->profileIds);
    }

    public function testReplaceUserWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->replaceUser('', [['roleId' => 'default', 'restrictedTo' => []]]);

            $this->fail('KuzzleTest::testReplaceUserWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::replaceUser: Unable to replace user: no id or body specified', $e->getMessage());
        }
    }

    public function testReplaceUserWithoutContent()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->replaceUser('profile', []);

            $this->fail('KuzzleTest::testReplaceUserWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::replaceUser: Unable to replace user: no id or body specified', $e->getMessage());
        }
    }

    function testDeleteProfile()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $profileId = uniqid();

        $httpRequest = [
            'route' => '/profiles/' . $profileId,
            'method' => 'DELETE',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'deleteProfile',
                'requestId' => $requestId,
                '_id' => $profileId,
            ],
            'query_parameters' => []
        ];
        $deleteResponse = [
            '_id' => $profileId,
            'found' => true
        ];
        $httpResponse = [
            'error' => null,
            'result' => $deleteResponse
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

        $result = $kuzzle->security->deleteProfile($profileId, ['requestId' => $requestId]);

        $this->assertEquals($profileId, $result['_id']);
    }

    public function testDeleteProfileWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->deleteProfile('', [['roleId' => 'default', 'restrictedTo' => []]]);

            $this->fail('KuzzleTest::testDeleteProfileWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::deleteProfile: Unable to delete profile: no id specified', $e->getMessage());
        }
    }

    function testDeleteUser()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $userId = uniqid();

        $httpRequest = [
            'route' => '/users/' . $userId,
            'method' => 'DELETE',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'deleteUser',
                'requestId' => $requestId,
                '_id' => $userId,
            ],
            'query_parameters' => []
        ];
        $deleteResponse = [
            '_id' => $userId,
            'found' => true
        ];
        $httpResponse = [
            'error' => null,
            'result' => $deleteResponse
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

        $result = $kuzzle->security->deleteUser($userId, ['requestId' => $requestId]);

        $this->assertEquals($userId, $result['_id']);
    }

    public function testDeleteUserWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->deleteUser('', [['roleId' => 'default', 'restrictedTo' => []]]);

            $this->fail('KuzzleTest::testDeleteUserWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::deleteUser: id is required', $e->getMessage());
        }
    }

    function testDeleteRole()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $roleId = uniqid();

        $httpRequest = [
            'route' => '/roles/' . $roleId,
            'method' => 'DELETE',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'deleteRole',
                'requestId' => $requestId,
                '_id' => $roleId,
            ],
            'query_parameters' => []
        ];
        $deleteResponse = [
            '_id' => $roleId,
            'found' => true
        ];
        $httpResponse = [
            'error' => null,
            'result' => $deleteResponse
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

        $result = $kuzzle->security->deleteRole($roleId, ['requestId' => $requestId]);

        $this->assertEquals($roleId, $result['_id']);
    }

    public function testDeleteRoleWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->deleteRole('', [['roleId' => 'default', 'restrictedTo' => []]]);

            $this->fail('KuzzleTest::testDeleteRoleWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::deleteRole: id is required', $e->getMessage());
        }
    }

    function testGetProfile()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $profileId = uniqid();
        $profileContent = [
            'policies' => [
                'roleId' => 'default',
                'restrictedTo' => [],
                'allowInternalIndex'=> true
            ]
        ];

        $httpRequest = [
            'route' => '/profiles/' . $profileId,
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'getProfile',
                'requestId' => $requestId,
                '_id' => $profileId,
            ],
            'query_parameters' => []
        ];
        $getResponse = [
            '_id' => $profileId,
            '_source' => $profileContent,
            '_meta' => []
        ];
        $httpResponse = [
            'error' => null,
            'result' => $getResponse
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

        $result = $kuzzle->security->getProfile($profileId, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Profile', $result);
        $this->assertAttributeEquals($profileId, '_id', $result);
        $this->assertAttributeEquals($profileContent['policies'], 'policies', $result);
    }

    public function testGetProfileWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->getProfile('', [['roleId' => 'default', 'restrictedTo' => []]]);

            $this->fail('KuzzleTest::testGetProfileWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::getProfile: id is required', $e->getMessage());
        }
    }

    function testGetRole()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $roleId = uniqid();
        $roleContent = [
            'allowInternalIndex' => false,
            'controllers' => [
                '*' => [
                    'actions'=> [
                        ['*' => true]
                    ]
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/roles/' . $roleId,
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'getRole',
                'requestId' => $requestId,
                '_id' => $roleId,
            ],
            'query_parameters' => []
        ];
        $getResponse = [
            '_id' => $roleId,
            '_source' => $roleContent,
            '_meta' => []
        ];
        $httpResponse = [
            'error' => null,
            'result' => $getResponse
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

        $result = $kuzzle->security->getRole($roleId, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Role', $result);
        $this->assertAttributeEquals($roleId, '_id', $result);
        $this->assertAttributeEquals($roleContent['controllers'], 'controllers', $result);
    }

    public function testGetRoleWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->getRole('', [['roleId' => 'default', 'restrictedTo' => []]]);

            $this->fail('KuzzleTest::testGetRoleWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::getRole: id is required', $e->getMessage());
        }
    }

    function testGetUser()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $userId = uniqid();
        $userContent = [
            'profileIds' => ['admin']
        ];

        $httpRequest = [
            'route' => '/users/' . $userId,
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'getUser',
                'requestId' => $requestId,
                '_id' => $userId,
            ],
            'query_parameters' => []
        ];
        $getResponse = [
            '_id' => $userId,
            '_source' => $userContent,
            '_meta' => []
        ];
        $httpResponse = [
            'error' => null,
            'result' => $getResponse
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

        $result = $kuzzle->security->getUser($userId, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\User', $result);
        $this->assertAttributeEquals($userId, '_id', $result);
        $this->assertAttributeEquals($userContent['profileIds'], 'profileIds', $result);
    }

    public function testGetUserWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->getUser('', [['roleId' => 'default', 'restrictedTo' => []]]);

            $this->fail('KuzzleTest::testGetUserWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::getUser: id is required', $e->getMessage());
        }
    }

    function testGetUserRights()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $userId = uniqid();
        $userRightsContent = [
            [
                "action" => "*",
                "collection" => "*",
                "controller" => "*",
                "index" => "*",
                "value" => "allowed"
            ]
        ];

        $httpRequest = [
            'route' => '/users/' . $userId . '/_rights',
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'getUserRights',
                'requestId' => $requestId,
                '_id' => $userId,
            ],
            'query_parameters' => []
        ];
        $getRightsResponse = [
            'hits' => $userRightsContent
        ];
        $httpResponse = [
            'error' => null,
            'result' => $getRightsResponse
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

        $result = $kuzzle->security->getUserRights($userId, ['requestId' => $requestId]);

        $this->assertEquals($userRightsContent, $result);
    }

    public function testGetUserRightsWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->getUserRights('', [['roleId' => 'default', 'restrictedTo' => []]]);

            $this->fail('KuzzleTest::testGetUserRightsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::getUserRights: id is required', $e->getMessage());
        }
    }

    function testGetProfileRights()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $profile_id = uniqid();
        $userRightsContent = [
            [
                "action" => "*",
                "collection" => "*",
                "controller" => "*",
                "index" => "*",
                "value" => "allowed"
            ]
        ];

        $httpRequest = [
            'route' => '/profiles/' . $profile_id . '/_rights',
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'getProfileRights',
                'requestId' => $requestId,
                '_id' => $profile_id,
            ],
            'query_parameters' => []
        ];
        $getRightsResponse = [
            'hits' => $userRightsContent
        ];
        $httpResponse = [
            'error' => null,
            'result' => $getRightsResponse
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

        $result = $kuzzle->security->getProfileRights($profile_id, ['requestId' => $requestId]);

        $this->assertEquals($userRightsContent, $result);
    }

    public function testGetProfileRightsWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->getProfileRights('', [['roleId' => 'default', 'restrictedTo' => []]]);

            $this->fail('KuzzleTest::testGetProfileRightsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::getProfileRights: id is required', $e->getMessage());
        }
    }

    function testSearchProfiles()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $filter = [
            'query' => [
                'bool' => [
                    'should' => [
                        'term' => ['foo' =>  'bar']
                    ]
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/profiles/_search',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'searchProfiles',
                'requestId' => $requestId,
                'body' => json_encode($filter)
            ],
            'query_parameters' => []
        ];
        $advancedSearchResponse = [
            'hits' => [
                0 => [
                    '_id' => 'test',
                    '_source' => [
                        'foo' => 'bar',
                        'policies' => [
                            [
                                'roleId' => 'default',
                                'restrictedTo' => [],
                                'allowInternalIndex'=> true
                            ]
                        ]
                    ],
                    '_meta' => []
                ],
                1 => [
                    '_id' => 'test1',
                    '_source' => [
                        'foo' => 'bar',
                        'policies' => [
                            [
                                'roleId' => 'default',
                                'restrictedTo' => [],
                                'allowInternalIndex'=> true
                            ]
                        ]
                    ],
                    '_meta' => []
                ]
            ],
            'total' => 2
        ];
        $httpResponse = [
            'error' => null,
            'result' => $advancedSearchResponse
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

        $searchResult = $kuzzle->security->searchProfiles($filter, ['scrollId' => 'YesScroll', 'requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Util\ProfilesSearchResult', $searchResult);
        $this->assertEquals(2, $searchResult->getTotal());

        $profiles = $searchResult->getProfiles();
        $this->assertInstanceOf('Kuzzle\Security\Profile', $profiles[0]);
        $this->assertAttributeEquals('test', '_id', $profiles[0]);
        $this->assertAttributeEquals('test1', '_id', $profiles[1]);
    }

    function testSearchRoles()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $filter = [
            'query' => [
                'bool' => [
                    'should' => [
                        'term' => ['foo' =>  'bar']
                    ]
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/roles/_search',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'searchRoles',
                'requestId' => $requestId,
                'body' => json_encode($filter)
            ],
            'query_parameters' => []
        ];
        $advancedSearchResponse = [
            'hits' => [
                0 => [
                    '_id' => 'test',
                    '_source' => [
                        'foo' => 'bar',
                        'allowInternalIndex' => false,
                        'controllers' => [
                            '*' => [
                                'actions'=> [
                                    ['*' => true]
                                ]
                            ]
                        ]
                    ],
                    '_meta' => []
                ],
                1 => [
                    '_id' => 'test1',
                    '_source' => [
                        'foo' => 'bar',
                        'allowInternalIndex' => false,
                        'controllers' => [
                            '*' => [
                                'actions'=> [
                                    ['*' => true]
                                ]
                            ]
                        ]
                    ],
                    '_meta' => []
                ]
            ],
            'total' => 2
        ];
        $httpResponse = [
            'error' => null,
            'result' => $advancedSearchResponse
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

        $searchResult = $kuzzle->security->searchRoles($filter, ['scrollId' => 'YesScroll', 'requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Util\RolesSearchResult', $searchResult);
        $this->assertEquals(2, $searchResult->getTotal());

        $profiles = $searchResult->getRoles();
        $this->assertInstanceOf('Kuzzle\Security\Role', $profiles[0]);
        $this->assertAttributeEquals('test', '_id', $profiles[0]);
        $this->assertAttributeEquals('test1', '_id', $profiles[1]);
    }

    function testSearchUsers()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $filter = [
            'query' => [
                'bool' => [
                    'should' => [
                        'term' => ['foo' =>  'bar']
                    ]
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/users/_search',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'searchUsers',
                'requestId' => $requestId,
                'body' => json_encode($filter)
            ],
            'query_parameters' => []
        ];
        $advancedSearchResponse = [
            'hits' => [
                0 => [
                    '_id' => 'test',
                    '_source' => [
                        'foo' => 'bar',
                        'profileIds' => ['default']
                    ],
                    '_meta' => [],
                ],
                1 => [
                    '_id' => 'test1',
                    '_source' => [
                        'foo' => 'bar',
                        'profileIds' => ['default']
                    ],
                    '_meta' => [],
                ]
            ],
            'total' => 2
        ];
        $httpResponse = [
            'error' => null,
            'result' => $advancedSearchResponse
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

        $searchResult = $kuzzle->security->searchUsers($filter, ['scrollId' => 'YesScroll', 'requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Util\UsersSearchResult', $searchResult);
        $this->assertEquals(2, $searchResult->getTotal());

        $profiles = $searchResult->getUsers();
        $this->assertInstanceOf('Kuzzle\Security\User', $profiles[0]);
        $this->assertAttributeEquals('test', '_id', $profiles[0]);
        $this->assertAttributeEquals('test1', '_id', $profiles[1]);
    }



    function testUpdateProfile()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $profileId = uniqid();
        $policiesBase = [
            'policies' => [
                'roleId' => 'anonymous',
                'restrictedTo' => [
                    ['index' => 'my-second-index', 'collection' => ['my-collection']]
                ]
            ]
        ];
        $policies = [
            'policies' => [
                'roleId' => 'default',
                'restrictedTo' => [
                    ['index' => 'my-index'],
                    ['index' => 'my-second-index', 'collection' => ['my-collection']]
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/profiles/' . $profileId . '/_update',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'updateProfile',
                'requestId' => $requestId,
                '_id' => $profileId,
                'body' => json_encode($policies)
            ],
            'query_parameters' => []
        ];
        $updateResponse = [
            '_id' => $profileId,
            '_meta' => [],
            '_version' => 2
        ];
        $httpResponse = [
            'error' => null,
            'result' => $updateResponse
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

        $result = $kuzzle->security->updateProfile($profileId, $policies, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Profile', $result);
        $this->assertAttributeEquals($profileId, '_id', $result);
    }

    public function testUpdateProfileWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateProfile('', [['roleId' => 'default', 'restrictedTo' => []]]);

            $this->fail('KuzzleTest::testUpdateProfileWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateProfile: id and body are required', $e->getMessage());
        }
    }

    public function testUpdateProfileWithoutBody()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateProfile('profile', []);

            $this->fail('KuzzleTest::testUpdateProfileWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateProfile: id and body are required', $e->getMessage());
        }
    }

    public function testUpdateProfileWithoutPoliciesInBody()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateProfile('profile', ['POLICIES']);

            $this->fail('KuzzleTest::testUpdateProfileWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateProfile: Unable to update given profile: body["policies"] property is required', $e->getMessage());
        }
    }

    function testUpdateRole()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $roleId = uniqid();
        $roleUpdateContent = [
            'foo' => 'bar'
        ];
        $roleContent = [
            'allowInternalIndex' => false,
            'controllers' => [
                '*' => [
                    'actions'=> [
                        ['*' => true]
                    ]
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/roles/' . $roleId . '/_update',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'updateRole',
                'requestId' => $requestId,
                '_id' => $roleId,
                'body' => json_encode($roleUpdateContent)
            ],
            'query_parameters' => []
        ];
        $updateResponse = [
            '_id' => $roleId,
            '_meta' => [],
            '_version' => 2
        ];
        $httpResponse = [
            'error' => null,
            'result' => $updateResponse
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

        $result = $kuzzle->security->updateRole($roleId, $roleUpdateContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Role', $result);
        $this->assertAttributeEquals($roleId, '_id', $result);
    }

    public function testUpdateRoleWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateRole('', [['roleId' => 'default', 'restrictedTo' => []]]);

            $this->fail('KuzzleTest::testUpdateRoleWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateRole: id and content are required', $e->getMessage());
        }
    }

    public function testUpdateRoleWithoutContent()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateRole('profile', []);

            $this->fail('KuzzleTest::testUpdateRoleWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateRole: id and content are required', $e->getMessage());
        }
    }

    function testUpdateUser()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $userId = uniqid();

        $userContent = [
            'profileIds' => [uniqid()]
        ];
        $userBaseContent = [
            'foo' => 'bar'
        ];

        $httpRequest = [
            'route' => '/users/' . $userId . '/_update',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'updateUser',
                'requestId' => $requestId,
                '_id' => $userId,
                'body' => json_encode($userContent)
            ],
            'query_parameters' => []
        ];
        $updateResponse = [
            '_id' => $userId,
            '_meta' => [],
            '_version' => 2
        ];
        $httpResponse = [
            'error' => null,
            'result' => $updateResponse
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

        $result = $kuzzle->security->updateUser($userId, $userContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\User', $result);
        $this->assertAttributeEquals($userId, '_id', $result);
    }

    public function testUpdateUserWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateUser('', [['roleId' => 'default', 'restrictedTo' => []]]);

            $this->fail('KuzzleTest::testUpdateUserWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateUser: id and body are required', $e->getMessage());
        }
    }

    public function testUpdateUserWithoutContent()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateUser('profile', []);

            $this->fail('KuzzleTest::testUpdateUserWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateUser: id and body are required', $e->getMessage());
        }
    }

    public function testCreateCredentials()
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
            'route' => '/credentials/local/42/_create',
            'request' => [
                'action' => 'createCredentials',
                'controller' => 'security',
                'volatile' => [],
                'requestId' => $options['requestId'],
                'strategy' => 'local',
                '_id' => 42,
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

        $kuzzle->security->createCredentials("local", "42", ["foo"=>"bar"], $options);
    }

    public function testCreateCredentialsWithoutStrategy()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createCredentials('', "42", ["foo"=>"bar"]);

            $this->fail('KuzzleTest::testCreateCredentialsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createCredentials: strategy, kuid and credentials are required', $e->getMessage());
        }
    }

    public function testCreateCredentialsWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createCredentials('local', "", ["foo"=>"bar"]);

            $this->fail('KuzzleTest::testCreateCredentialsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createCredentials: strategy, kuid and credentials are required', $e->getMessage());
        }
    }

    public function testCreateCredentialsWithoutCredentials()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->createCredentials('local', "42", []);

            $this->fail('KuzzleTest::testCreateCredentialsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::createCredentials: strategy, kuid and credentials are required', $e->getMessage());
        }
    }

    public function testDeleteCredentials()
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
            'route' => '/credentials/local/42',
            'request' => [
                'action' => 'deleteCredentials',
                'controller' => 'security',
                'volatile' => [],
                'requestId' => $options['requestId'],
            ],
            'method' => 'DELETE',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => [
                "acknowledged" => true,
            ]
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $kuzzle->security->deleteCredentials("local", "42", $options);
    }

    public function testDeleteCredentialsWithoutStrategy()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->deleteCredentials('', "42");

            $this->fail('KuzzleTest::testDeleteCredentialsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::deleteCredentials: strategy and kuid are required', $e->getMessage());
        }
    }

    public function testDeleteCredentialsWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->deleteCredentials('local', "");

            $this->fail('KuzzleTest::testDeleteCredentialsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::deleteCredentials: strategy and kuid are required', $e->getMessage());
        }
    }

    public function testGetAllCredentialFields()
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
            'route' => '/credentials/_fields',
            'request' => [
                'action' => 'getAllCredentialFields',
                'controller' => 'security',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => [
                "local" => [
                    "username",
                    "password"
                ],
            ]
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $kuzzle->security->getAllCredentialFields($options);
    }

    public function testGetCredentialFields()
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
            'route' => '/credentials/local/_fields',
            'request' => [
                'action' => 'getCredentialFields',
                'controller' => 'security',
                'volatile' => [],
                'requestId' => $options['requestId'],
                'strategy' => 'local'

            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => [
                "local" => [
                    "username",
                    "password"
                ],
            ]
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $kuzzle->security->getCredentialFields('local', $options);
    }

    public function testgetCredentialsFieldsWithoutStrategy()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->getCredentialFields('');

            $this->fail('KuzzleTest::testgetCredentialsFieldsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::getCredentialFields: strategy is required', $e->getMessage());
        }
    }

    public function testGetCredentials()
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
            'route' => '/credentials/local/42',
            'request' => [
                'action' => 'getCredentials',
                'controller' => 'security',
                'volatile' => [],
                'requestId' => $options['requestId'],
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

        $kuzzle->security->getCredentials('local', '42', $options);
    }

    public function testGetCredentialsWithoutStrategy()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->getCredentials('', "42");

            $this->fail('KuzzleTest::testGetCredentialsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::getCredentials: strategy and kuid are required', $e->getMessage());
        }
    }

    public function testGetCredentialsWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->getCredentials('local', "");

            $this->fail('KuzzleTest::testGetCredentialsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::getCredentials: strategy and kuid are required', $e->getMessage());
        }
    }

    public function testGetCredentialsById()
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
            'route' => '/credentials/local/42/_byId',
            'request' => [
                'action' => 'getCredentialsById',
                'controller' => 'security',
                'volatile' => [],
                'requestId' => $options['requestId'],
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

        $kuzzle->security->getCredentialsById('local', '42', $options);
    }

    public function testGetCredentialsByIdWithoutStrategy()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->getCredentialsById('', "42");

            $this->fail('KuzzleTest::testGetCredentialsByIdWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::getCredentialsById: strategy and userId are required', $e->getMessage());
        }
    }

    public function testGetCredentialsByIdWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->getCredentialsById('local', "");

            $this->fail('KuzzleTest::testGetCredentialsByIdWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::getCredentialsById: strategy and userId are required', $e->getMessage());
        }
    }

    public function testHasCredentials()
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
            'route' => '/credentials/local/42/_exists',
            'request' => [
                'action' => 'hasCredentials',
                'controller' => 'security',
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

        $kuzzle->security->hasCredentials('local', '42', $options);
    }

    public function testHasCredentialsWithoutStrategy()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->hasCredentials('', "42");

            $this->fail('KuzzleTest::testHasCredentialsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::hasCredentials: strategy and kuid are required', $e->getMessage());
        }
    }

    public function testHasCredentialsWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->hasCredentials('local', "");

            $this->fail('KuzzleTest::testHasCredentialsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::hasCredentials: strategy and kuid are required', $e->getMessage());
        }
    }

    public function testUpdateCredentials()
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
            'route' => '/credentials/local/42/_update',
            'request' => [
                'action' => 'updateCredentials',
                'controller' => 'security',
                'volatile' => [],
                'body' => json_encode(["foo" => "bar"]),
                'requestId' => $options['requestId']
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

        $kuzzle->security->updateCredentials('local', '42', ["foo" => "bar"], $options);
    }

    public function testUpdateCredentialsWithoutStrategy()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateCredentials('', "42", ["foo"=>"bar"]);

            $this->fail('KuzzleTest::testUpdateCredentialsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateCredentials: strategy, kuid and credentials are required', $e->getMessage());
        }
    }

    public function testUpdateCredentialsWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateCredentials('local', "", ["foo"=>"bar"]);

            $this->fail('KuzzleTest::testUpdateCredentialsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateCredentials: strategy, kuid and credentials are required', $e->getMessage());
        }
    }

    public function testUpdateCredentialsWithoutCredentials()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateCredentials('local', "42", []);

            $this->fail('KuzzleTest::testUpdateCredentialsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateCredentials: strategy, kuid and credentials are required', $e->getMessage());
        }
    }

    public function testValidateCredentials()
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
            'route' => '/credentials/local/42/_validate',
            'request' => [
                'action' => 'validateCredentials',
                'controller' => 'security',
                'volatile' => [],
                'body' => json_encode(["foo" => "bar"]),
                'requestId' => $options['requestId']
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

        $kuzzle->security->validateCredentials('local', '42', ["foo" => "bar"], $options);
    }

    public function testValidateCredentialsWithoutStrategy()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->validateCredentials('', "42", ["foo"=>"bar"]);

            $this->fail('KuzzleTest::testValidateCredentialsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::validateCredentials: strategy, kuid and credentials are required', $e->getMessage());
        }
    }

    public function testValidateCredentialsWithoutId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->validateCredentials('local', "", ["foo"=>"bar"]);

            $this->fail('KuzzleTest::testValidateCredentialsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::validateCredentials: strategy, kuid and credentials are required', $e->getMessage());
        }
    }

    public function testValidateCredentialsWithoutCredentials()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->validateCredentials('local', "42", []);

            $this->fail('KuzzleTest::testValidateCredentialsWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::validateCredentials: strategy, kuid and credentials are required', $e->getMessage());
        }
    }

    public function testMGetProfiles()
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
            'route' => '/profiles/_mGet',
            'request' => [
                'action' => 'mGetProfiles',
                'controller' => 'security',
                'volatile' => [],
                'body' => json_encode(['ids' => ["foo", "bar"]]),
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => [
                "hits" => [
                    [
                        "_id" => "foo",
                        "_source" => [
                            'policies' => [
                                'roleId' => 'default',
                                'restrictedTo' => [],
                                'allowInternalIndex'=> true
                            ]
                        ]
                    ],
                    [
                        "_id" => "bar",
                        "_source" => [
                            'policies' => [
                                'roleId' => 'default',
                                'restrictedTo' => [],
                                'allowInternalIndex'=> true
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $result = $kuzzle->security->mGetProfiles(["foo", "bar"], $options);
        $this->assertEquals($httpResponse['result']['hits'][0]['_id'], $result[0]->_id);
    }

    public function testMGetProfilesWithoutIds()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->mGetProfiles([]);

            $this->fail('KuzzleTest::testMGetProfilesWithoutId => Should raise an exception (could not be called without profile id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::mGetProfiles: Unable to get profiles: no ids specified', $e->getMessage());
        }
    }

    public function testMGetRoles()
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
            'route' => '/roles/_mGet',
            'request' => [
                'action' => 'mGetRoles',
                'controller' => 'security',
                'volatile' => [],
                'body' => json_encode(['ids' => ["foo", "bar"]]),
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => [
                "hits" => [
                    [
                        "_id" => "foo",
                        "_source" => [
                            'controllers' => [
                                '*' => [
                                    'actions'=> [
                                        ['*' => true]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "_id" => "bar",
                        "_source" => [
                            'controllers' => [
                                '*' => [
                                    'actions'=> [
                                        ['*' => true]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $result = $kuzzle->security->mGetRoles(["foo", "bar"], $options);
        $this->assertEquals($httpResponse['result']['hits'][0]['_id'], $result[0]->_id);
    }

    public function testMGetRolesWithoutIds()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->mGetRoles([]);

            $this->fail('KuzzleTest::testMGetRolesWithoutId => Should raise an exception (could not be called without roles id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::mGetRoles: Unable to get roles: no ids specified', $e->getMessage());
        }
    }

    public function testMDeleteRoles()
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
            'route' => '/roles/_mDelete',
            'request' => [
                'action' => 'mDeleteRoles',
                'controller' => 'security',
                'volatile' => [],
                'body' => json_encode(['ids' => ["foo", "bar"]]),
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => ["foo", "bar"]
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $result = $kuzzle->security->mDeleteRoles(["foo", "bar"], $options);
        $this->assertEquals($httpResponse['result'][0], $result[0]);
    }

    public function testMDeleteRolesWithoutIds()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->mDeleteRoles([]);

            $this->fail('KuzzleTest::testMDeleteRolesWithoutId => Should raise an exception (could not be called without roles id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::mDeleteRoles: Unable to delete roles: no ids specified', $e->getMessage());
        }
    }

    public function testMDeleteProfiles()
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
            'route' => '/profiles/_mDelete',
            'request' => [
                'action' => 'mDeleteProfiles',
                'controller' => 'security',
                'volatile' => [],
                'body' => json_encode(['ids' => ["foo", "bar"]]),
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => ["foo", "bar"]
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $result = $kuzzle->security->mDeleteProfiles(["foo", "bar"], $options);
        $this->assertEquals($httpResponse['result'][0], $result[0]);
    }

    public function testMDeleteProfilesWithoutIds()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->mDeleteProfiles([]);

            $this->fail('KuzzleTest::testMDeleteProfilesWithoutId => Should raise an exception (could not be called without profiles id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::mDeleteProfiles: Unable to delete profiles: no ids specified', $e->getMessage());
        }
    }

    public function testMDeleteUsers()
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
            'route' => '/users/_mDelete',
            'request' => [
                'action' => 'mDeleteUsers',
                'controller' => 'security',
                'volatile' => [],
                'body' => json_encode(['ids' => ["foo", "bar"]]),
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => ["foo", "bar"]
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $result = $kuzzle->security->mDeleteUsers(["foo", "bar"], $options);
        $this->assertEquals($httpResponse['result'][0], $result[0]);
    }

    public function testMDeleteUsersWithoutIds()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->mDeleteUsers([]);

            $this->fail('KuzzleTest::testMDeleteUsersWithoutId => Should raise an exception (could not be called without users id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::mDeleteUsers: Unable to delete users: no ids specified', $e->getMessage());
        }
    }

    function testGetProfileMapping()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $httpRequest = [
            'route' => '/profiles/_mapping',
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'getProfileMapping',
                'requestId' => $requestId,
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            'mapping' => 'toto',
        ];
        $httpResponse = [
            'error' => null,
            'result' => $saveResponse
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

        $result = $kuzzle->security->getProfileMapping(['requestId' => $requestId]);

        $this->assertEquals($saveResponse['mapping'], $result);
    }

    function testGetRoleMapping()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $httpRequest = [
            'route' => '/roles/_mapping',
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'getRoleMapping',
                'requestId' => $requestId,
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            'mapping' => 'toto',
        ];
        $httpResponse = [
            'error' => null,
            'result' => $saveResponse
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

        $result = $kuzzle->security->getRoleMapping(['requestId' => $requestId]);

        $this->assertEquals($saveResponse['mapping'], $result);
    }

    function testGetUserMapping()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $httpRequest = [
            'route' => '/users/_mapping',
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'getUserMapping',
                'requestId' => $requestId,
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            'mapping' => 'toto',
        ];
        $httpResponse = [
            'error' => null,
            'result' => $saveResponse
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

        $result = $kuzzle->security->getUserMapping(['requestId' => $requestId]);

        $this->assertEquals($saveResponse['mapping'], $result);
    }

    function testUpdateProfileMapping()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $mapping = [
            "properties" => [
                'field' => [
                    'type' => 'prank',
                    'other' => 'anotherPrank'
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/profiles/_mapping',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'updateProfileMapping',
                'requestId' => $requestId,
                'body' => json_encode($mapping)
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            "acknowledged" => true
        ];
        $httpResponse = [
            'error' => null,
            'result' => $saveResponse
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

        $result = $kuzzle->security->updateProfileMapping($mapping, ['requestId' => $requestId]);

        $this->assertEquals(true, $result['acknowledged']);
    }

    public function testUpdateProfileMappingWithoutMapping()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateProfileMapping([]);

            $this->fail('KuzzleTest::testMDeleteUsersWithoutId => Should raise an exception (could not be called without users id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateProfileMapping: body is required', $e->getMessage());
        }
    }

    public function testUpdateProfileMappingWithMalformedBody()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateProfileMapping(["malformed" => "content"]);

            $this->fail('KuzzleTest::testMDeleteUsersWithMalformedBody => Should raise an exception (could not be called without users id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateProfileMapping: Unable to update given profile mapping: body["properties"] property is required', $e->getMessage());
        }
    }

    function testUpdateRoleMapping()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $mapping = [
            "properties" => [
                'field' => [
                    'type' => 'prank',
                    'other' => 'anotherPrank'
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/roles/_mapping',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'updateRoleMapping',
                'requestId' => $requestId,
                'body' => json_encode($mapping)
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            "acknowledged" => true
        ];
        $httpResponse = [
            'error' => null,
            'result' => $saveResponse
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

        $result = $kuzzle->security->updateRoleMapping($mapping, ['requestId' => $requestId]);

        $this->assertEquals(true, $result['acknowledged']);
    }

    public function testUpdateRoleMappingWithoutMapping()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateRoleMapping([]);

            $this->fail('KuzzleTest::testMDeleteUsersWithoutId => Should raise an exception (could not be called without users id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateRoleMapping: mapping is required', $e->getMessage());
        }
    }

    public function testUpdateRoleMappingWithMalformedBody()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateRoleMapping(["malformed" => "content"]);

            $this->fail('KuzzleTest::testMDeleteRolesWithMalformedBody => Should raise an exception (could not be called without users id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateRoleMapping: Unable to update given role mapping: body["properties"] property is required', $e->getMessage());
        }
    }

    function testUpdateUserMapping()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $mapping = [
            "properties" => [
                'field' => [
                    'type' => 'prank',
                    'other' => 'anotherPrank'
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/users/_mapping',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'updateUserMapping',
                'requestId' => $requestId,
                'body' => json_encode($mapping)
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            "acknowledged" => true
        ];
        $httpResponse = [
            'error' => null,
            'result' => $saveResponse
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

        $result = $kuzzle->security->updateUserMapping($mapping, ['requestId' => $requestId]);

        $this->assertEquals(true, $result['acknowledged']);
    }

    public function testUpdateUserMappingWithoutMapping()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateUserMapping([]);

            $this->fail('KuzzleTest::testMDeleteUsersWithoutId => Should raise an exception (could not be called without users id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateUserMapping: mapping is required', $e->getMessage());
        }
    }

    public function testUpdateUserMappingWithMalformedBody()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->security->updateUserMapping(["malformed" => "content"]);

            $this->fail('KuzzleTest::testMDeleteUsersWithMalformedBody => Should raise an exception (could not be called without users id or policies)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Security::updateUserMapping: Unable to update given user mapping: body["properties"] property is required', $e->getMessage());
        }
    }
}
