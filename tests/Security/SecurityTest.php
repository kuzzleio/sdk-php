<?php

use Kuzzle\Kuzzle;
use Kuzzle\Security\Security;
use Kuzzle\Security\User;
use Kuzzle\Security\Profile;
use Kuzzle\Security\Role;
use Kuzzle\Util\UsersSearchResult;
use Kuzzle\Util\ProfilesSearchResult;
use Kuzzle\Util\RolesSearchResult;

class SecurityTest extends \PHPUnit_Framework_TestCase
{
    function testCreateProfile()
    {
        $url = KuzzleTest::FAKE_KUZZLE_URL;
        $requestId = uniqid();

        $profileId = uniqid();
        $profileContent = [
            'policies' => [
                [
                    '_id' => 'default',
                    'restrictedTo' => [],
                    'allowInternalIndex'=> true
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/api/1.0/profiles/_create',
            'method' => 'POST',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'createProfile',
                'requestId' => $requestId,
                '_id' => $profileId,
                'body' => $profileContent
            ]
        ];
        $saveResponse = [
            '_id' => $profileId,
            '_source' => $profileContent,
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->createProfile($profileId, $profileContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Profile', $result);
        $this->assertAttributeEquals($profileId, 'id', $result);
        $this->assertAttributeEquals($profileContent, 'content', $result);
    }

    function testCreateOrReplaceProfile()
    {
        $url = KuzzleTest::FAKE_KUZZLE_URL;
        $requestId = uniqid();

        $profileId = uniqid();
        $profileContent = [
            'policies' => [
                [
                    '_id' => 'default',
                    'restrictedTo' => [],
                    'allowInternalIndex'=> true
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/api/1.0/profiles/' . $profileId . '/_createOrReplace',
            'method' => 'PUT',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'createOrReplaceProfile',
                'requestId' => $requestId,
                '_id' => $profileId,
                'body' => $profileContent
            ]
        ];
        $saveResponse = [
            '_id' => $profileId,
            '_source' => $profileContent,
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->createProfile($profileId, $profileContent, [
            'replaceIfExist' => true,
            'requestId' => $requestId
        ]);

        $this->assertInstanceOf('Kuzzle\Security\Profile', $result);
        $this->assertAttributeEquals($profileId, 'id', $result);
        $this->assertAttributeEquals($profileContent, 'content', $result);
    }

    function testCreateRole()
    {
        $url = KuzzleTest::FAKE_KUZZLE_URL;
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
            'route' => '/api/1.0/roles/_create',
            'method' => 'POST',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'createRole',
                'requestId' => $requestId,
                '_id' => $roleId,
                'body' => $roleContent
            ]
        ];
        $saveResponse = [
            '_id' => $roleId,
            '_source' => $roleContent,
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->createRole($roleId, $roleContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Role', $result);
        $this->assertAttributeEquals($roleId, 'id', $result);
        $this->assertAttributeEquals($roleContent, 'content', $result);
    }

    function testCreateOrReplaceRole()
    {
        $url = KuzzleTest::FAKE_KUZZLE_URL;
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
            'route' => '/api/1.0/roles/' . $roleId . '/_createOrReplace',
            'method' => 'PUT',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'createOrReplaceRole',
                'requestId' => $requestId,
                '_id' => $roleId,
                'body' => $roleContent
            ]
        ];
        $saveResponse = [
            '_id' => $roleId,
            '_source' => $roleContent,
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->createRole($roleId, $roleContent, [
            'replaceIfExist' => true,
            'requestId' => $requestId
        ]);

        $this->assertInstanceOf('Kuzzle\Security\Role', $result);
        $this->assertAttributeEquals($roleId, 'id', $result);
        $this->assertAttributeEquals($roleContent, 'content', $result);
    }

    function testCreateUser()
    {
        $url = KuzzleTest::FAKE_KUZZLE_URL;
        $requestId = uniqid();

        $userId = uniqid();
        $userContent = [
            'profileId' => 'admin'
        ];

        $httpRequest = [
            'route' => '/api/1.0/users/_create',
            'method' => 'POST',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'createUser',
                'requestId' => $requestId,
                '_id' => $userId,
                'body' => $userContent
            ]
        ];
        $saveResponse = [
            '_id' => $userId,
            '_source' => $userContent,
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->createUser($userId, $userContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\User', $result);
        $this->assertAttributeEquals($userId, 'id', $result);
        $this->assertAttributeEquals($userContent, 'content', $result);
    }

    function testCreateOrReplaceUser()
    {
        $url = KuzzleTest::FAKE_KUZZLE_URL;
        $requestId = uniqid();

        $userId = uniqid();
        $userContent = [
            'profileId' => 'admin'
        ];

        $httpRequest = [
            'route' => '/api/1.0/users/' . $userId,
            'method' => 'PUT',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'createOrReplaceUser',
                'requestId' => $requestId,
                '_id' => $userId,
                'body' => $userContent
            ]
        ];
        $saveResponse = [
            '_id' => $userId,
            '_source' => $userContent,
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->createUser($userId, $userContent, [
            'replaceIfExist' => true,
            'requestId' => $requestId
        ]);

        $this->assertInstanceOf('Kuzzle\Security\User', $result);
        $this->assertAttributeEquals($userId, 'id', $result);
        $this->assertAttributeEquals($userContent, 'content', $result);
    }

    function testDeleteProfile()
    {
        $url = KuzzleTest::FAKE_KUZZLE_URL;
        $requestId = uniqid();

        $profileId = uniqid();

        $httpRequest = [
            'route' => '/api/1.0/profiles/' . $profileId,
            'method' => 'DELETE',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'deleteProfile',
                'requestId' => $requestId,
                '_id' => $profileId,
            ]
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->deleteProfile($profileId, ['requestId' => $requestId]);

        $this->assertEquals($profileId, $result);
    }

    function testDeleteUser()
    {
        $url = KuzzleTest::FAKE_KUZZLE_URL;
        $requestId = uniqid();

        $userId = uniqid();

        $httpRequest = [
            'route' => '/api/1.0/users/' . $userId,
            'method' => 'DELETE',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'deleteUser',
                'requestId' => $requestId,
                '_id' => $userId,
            ]
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->deleteUser($userId, ['requestId' => $requestId]);

        $this->assertEquals($userId, $result);
    }

    function testDeleteRole()
    {
        $url = KuzzleTest::FAKE_KUZZLE_URL;
        $requestId = uniqid();

        $roleId = uniqid();

        $httpRequest = [
            'route' => '/api/1.0/roles/' . $roleId,
            'method' => 'DELETE',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'deleteRole',
                'requestId' => $requestId,
                '_id' => $roleId,
            ]
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->deleteRole($roleId, ['requestId' => $requestId]);

        $this->assertEquals($roleId, $result);
    }

    function testGetProfile()
    {
        $url = KuzzleTest::FAKE_KUZZLE_URL;
        $requestId = uniqid();

        $profileId = uniqid();
        $profileContent = [
            'policies' => [
                [
                    '_id' => 'default',
                    'restrictedTo' => [],
                    'allowInternalIndex'=> true
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/api/1.0/profiles/' . $profileId,
            'method' => 'GET',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'getProfile',
                'requestId' => $requestId,
                '_id' => $profileId,
            ]
        ];
        $getResponse = [
            '_id' => $profileId,
            '_source' => $profileContent
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->getProfile($profileId, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Profile', $result);
        $this->assertAttributeEquals($profileId, 'id', $result);
        $this->assertAttributeEquals($profileContent, 'content', $result);
    }

    function testGetRole()
    {
        $url = KuzzleTest::FAKE_KUZZLE_URL;
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
            'route' => '/api/1.0/roles/' . $roleId,
            'method' => 'GET',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'getRole',
                'requestId' => $requestId,
                '_id' => $roleId,
            ]
        ];
        $getResponse = [
            '_id' => $roleId,
            '_source' => $roleContent
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->getRole($roleId, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Role', $result);
        $this->assertAttributeEquals($roleId, 'id', $result);
        $this->assertAttributeEquals($roleContent, 'content', $result);
    }

    function testGetUser()
    {
        $url = KuzzleTest::FAKE_KUZZLE_URL;
        $requestId = uniqid();

        $userId = uniqid();
        $userContent = [
            'profileId' => 'admin'
        ];

        $httpRequest = [
            'route' => '/api/1.0/users/' . $userId,
            'method' => 'GET',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'getUser',
                'requestId' => $requestId,
                '_id' => $userId,
            ]
        ];
        $getResponse = [
            '_id' => $userId,
            '_source' => $userContent
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->getUser($userId, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\User', $result);
        $this->assertAttributeEquals($userId, 'id', $result);
        $this->assertAttributeEquals($userContent, 'content', $result);
    }

    function testGetUserRights()
    {
        $url = KuzzleTest::FAKE_KUZZLE_URL;
        $requestId = uniqid();

        $userId = uniqid();
        $userRightsContent = [[
            "action" => "*",
            "collection" => "*",
            "controller" => "*",
            "index" => "*",
            "value" => "allowed"
        ]];

        $httpRequest = [
            'route' => '/api/1.0/users/' . $userId . '/_rights',
            'method' => 'GET',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'getUserRights',
                'requestId' => $requestId,
                '_id' => $userId,
            ]
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->getUserRights($userId, ['requestId' => $requestId]);

        $this->assertEquals($userRightsContent, $result);
    }
}