<?php

use Kuzzle\Kuzzle;
use Kuzzle\Security\Security;

class SecurityTest extends \PHPUnit_Framework_TestCase
{
    function testCreateProfile()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $profileId = uniqid();
        $policies = [
            [
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
                'body' => [ 'policies' => $policies]
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            '_id' => $profileId,
            '_source' => [ 'policies' => $policies ],
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->createProfile($profileId, $policies, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Profile', $result);
        $this->assertAttributeEquals($profileId, 'id', $result);
        $this->assertAttributeEquals(['policies' => $policies], 'content', $result);
    }

    function testCreateOrReplaceProfile()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $profileId = uniqid();
        $policies = [
            [
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
                'body' => [ 'policies' => $policies]
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            '_id' => $profileId,
            '_source' => [ 'policies' => $policies ],
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->createProfile($profileId, $policies, [
            'replaceIfExist' => true,
            'requestId' => $requestId
        ]);

        $this->assertInstanceOf('Kuzzle\Security\Profile', $result);
        $this->assertAttributeEquals($profileId, 'id', $result);
        $this->assertAttributeEquals([ 'policies' => $policies ], 'content', $result);
    }

    function testCreateRole()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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
            'route' => '/roles/' . $roleId . '/_create',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'createRole',
                'requestId' => $requestId,
                '_id' => $roleId,
                'body' => $roleContent
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
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'createOrReplaceRole',
                'requestId' => $requestId,
                '_id' => $roleId,
                'body' => $roleContent
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
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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
                'body' => $userContent
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            '_id' => $userId,
            '_source' => $userContent,
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->createUser($userId, $userContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\User', $result);
        $this->assertAttributeEquals($userId, 'id', $result);
        $this->assertAttributeEquals($userContent, 'content', $result);
    }

    function testCreateRestrictedUser()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $userId = uniqid();
        $userContent = [
            'some' => 'content'
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
                'body' => $userContent
            ],
            'query_parameters' => []
        ];
        $saveResponse = [
            '_id' => $userId,
            '_source' => $userContent,
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->createRestrictedUser($userId, $userContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\User', $result);
        $this->assertAttributeEquals($userId, 'id', $result);
        $this->assertAttributeEquals($userContent, 'content', $result);
    }

    function testReplaceUser()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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
                'body' => $userContent
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->replaceUser($userId, $userContent, ['requestId' => $requestId]);

        $this->assertEquals($userContent, $result->getContent());
    }

    function testDeleteProfile()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->deleteProfile($profileId, ['requestId' => $requestId]);

        $this->assertEquals($profileId, $result);
    }

    function testScrollProfiles()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $scrollId = uniqid();

        $httpSearchRequest = [
            'route' => '/profiles/_search',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'searchProfiles',
                'requestId' => $requestId,
                'body' => (object)[]
            ],
            'query_parameters' => [
                'from' => 0,
                'size' => 1,
                'scroll' => '30s'
            ]
        ];

        $httpScrollRequest = [
            'route' => '/profiles/_scroll/' . $scrollId,
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'scrollProfiles',
                'requestId' => $requestId,
            ],
            'query_parameters' => []
        ];
        $searchResponse = [
            'hits' => [
                0 => [
                    '_id' => 'test',
                    '_source' => [
                        'foo' => 'bar'
                    ],
                    '_meta' => []
                ]
            ],
            'scrollId' => $scrollId,
            'total' => 2
        ];
        $scrollResponse = [
            'hits' => [
                0 => [
                    '_id' => 'test1',
                    '_source' => [
                        'foo' => 'bar'
                    ],
                    '_meta' => []
                ]
            ],
            'total' => 2
        ];
        $httpSearchResponse = [
            'error' => null,
            'result' => $searchResponse
        ];
        $httpScrollResponse = [
            'error' => null,
            'result' => $scrollResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->at(0))
            ->method('emitRestRequest')
            ->with($httpSearchRequest)
            ->willReturn($httpSearchResponse);

        $kuzzle
            ->expects($this->at(1))
            ->method('emitRestRequest')
            ->with($httpScrollRequest)
            ->willReturn($httpScrollResponse);

        $security = new Security($kuzzle);
        $searchProfilesResult = $security->searchProfiles([], ['scroll' => '30s', 'from' => 0, 'size' => 1, 'requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Util\ProfilesSearchResult', $searchProfilesResult);
        $this->assertInternalType('array', $searchProfilesResult->getProfiles());
        $this->assertEquals(1, count($searchProfilesResult->getProfiles()));
        $this->assertInstanceOf('Kuzzle\Security\Profile', $searchProfilesResult->getProfiles()[0]);
        $this->assertAttributeEquals('test', 'id', $searchProfilesResult->getProfiles()[0]);
        $this->assertNotNull($searchProfilesResult->getScrollId());

        $scrollProfilesResult = $security->scrollProfiles($searchProfilesResult->getScrollId(), ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Util\ProfilesSearchResult', $scrollProfilesResult);
        $this->assertInternalType('array', $scrollProfilesResult->getProfiles());
        $this->assertEquals(1, count($scrollProfilesResult->getProfiles()));
        $this->assertInstanceOf('Kuzzle\Security\Profile', $scrollProfilesResult->getProfiles()[0]);
        $this->assertAttributeEquals('test1', 'id', $scrollProfilesResult->getProfiles()[0]);
        $this->assertNotNull($scrollProfilesResult->getScrollId());
    }

    function testDeleteUser()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->deleteUser($userId, ['requestId' => $requestId]);

        $this->assertEquals($userId, $result);
    }

    function testScrollUsers()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $scrollId = uniqid();

        $httpSearchRequest = [
            'route' => '/users/_search',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'searchUsers',
                'requestId' => $requestId,
                'body' => (object)[]
            ],
            'query_parameters' => [
                'from' => 0,
                'size' => 1,
                'scroll' => '30s'
            ]
        ];

        $httpScrollRequest = [
            'route' => '/users/_scroll/' . $scrollId,
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'security',
                'action' => 'scrollUsers',
                'requestId' => $requestId,
            ],
            'query_parameters' => []
        ];
        $searchResponse = [
            'hits' => [
                0 => [
                    '_id' => 'test',
                    '_source' => [
                        'foo' => 'bar'
                    ],
                    '_meta' => []
                ]
            ],
            'scrollId' => $scrollId,
            'total' => 2
        ];
        $scrollResponse = [
            'hits' => [
                0 => [
                    '_id' => 'test1',
                    '_source' => [
                        'foo' => 'bar'
                    ],
                    '_meta' => []
                ]
            ],
            'total' => 2
        ];
        $httpSearchResponse = [
            'error' => null,
            'result' => $searchResponse
        ];
        $httpScrollResponse = [
            'error' => null,
            'result' => $scrollResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->at(0))
            ->method('emitRestRequest')
            ->with($httpSearchRequest)
            ->willReturn($httpSearchResponse);

        $kuzzle
            ->expects($this->at(1))
            ->method('emitRestRequest')
            ->with($httpScrollRequest)
            ->willReturn($httpScrollResponse);

        $security = new Security($kuzzle);
        $searchUsersResult = $security->searchUsers([], ['scroll' => '30s', 'from' => 0, 'size' => 1, 'requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Util\UsersSearchResult', $searchUsersResult);
        $this->assertInternalType('array', $searchUsersResult->getUsers());
        $this->assertEquals(1, count($searchUsersResult->getUsers()));
        $this->assertInstanceOf('Kuzzle\Security\User', $searchUsersResult->getUsers()[0]);
        $this->assertAttributeEquals('test', 'id', $searchUsersResult->getUsers()[0]);
        $this->assertNotNull($searchUsersResult->getScrollId());
        
        $scrollUsersResult = $security->scrollUsers($searchUsersResult->getScrollId(), ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Util\UsersSearchResult', $scrollUsersResult);
        $this->assertInternalType('array', $scrollUsersResult->getUsers());
        $this->assertEquals(1, count($scrollUsersResult->getUsers()));
        $this->assertInstanceOf('Kuzzle\Security\User', $scrollUsersResult->getUsers()[0]);
        $this->assertAttributeEquals('test1', 'id', $scrollUsersResult->getUsers()[0]);
        $this->assertNotNull($searchUsersResult->getScrollId());
    }

    function testDeleteRole()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->deleteRole($roleId, ['requestId' => $requestId]);

        $this->assertEquals($roleId, $result);
    }

    function testGetProfile()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $profileId = uniqid();
        $profileContent = [
            'policies' => [
                [
                    'roleId' => 'default',
                    'restrictedTo' => [],
                    'allowInternalIndex'=> true
                ]
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->fetchProfile($profileId, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Profile', $result);
        $this->assertAttributeEquals($profileId, 'id', $result);
        $this->assertAttributeEquals($profileContent, 'content', $result);
    }

    function testGetRole()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->fetchRole($roleId, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Role', $result);
        $this->assertAttributeEquals($roleId, 'id', $result);
        $this->assertAttributeEquals($roleContent, 'content', $result);
    }

    function testGetUser()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->fetchUser($userId, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\User', $result);
        $this->assertAttributeEquals($userId, 'id', $result);
        $this->assertAttributeEquals($userContent, 'content', $result);
    }

    function testGetUserRights()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->getUserRights($userId, ['requestId' => $requestId]);

        $this->assertEquals($userRightsContent, $result);
    }

    function testIsActionAllowed()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

        $kuzzle = new Kuzzle($url);
        $security = $kuzzle->security();

        $userRights = [
            [
                "controller" => "server",
                "action" => "now",
                "collection" => "*",
                "index" => "*",
                "value" => "allowed"
            ],
            [
                "controller" => "document",
                "action" => "get",
                "collection" => "*",
                "index" => "*",
                "value" => "conditional"
            ]
        ];

        $result = $security->isActionAllowed($userRights, 'server', 'now');
        $this->assertEquals(Security::ACTION_ALLOWED, $result);

        $result = $security->isActionAllowed($userRights, 'document', 'get');
        $this->assertEquals(Security::ACTION_CONDITIONAL, $result);

        $result = $security->isActionAllowed($userRights, 'index', 'list');
        $this->assertEquals(Security::ACTION_DENIED, $result);
    }

    function testSearchProfiles()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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
                'body' => $filter
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $searchResult = $security->searchProfiles($filter, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Util\ProfilesSearchResult', $searchResult);
        $this->assertEquals(2, $searchResult->getTotal());

        $profiles = $searchResult->getProfiles();
        $this->assertInstanceOf('Kuzzle\Security\Profile', $profiles[0]);
        $this->assertAttributeEquals('test', 'id', $profiles[0]);
        $this->assertAttributeEquals('test1', 'id', $profiles[1]);
    }

    function testSearchRoles()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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
                'body' => $filter
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $searchResult = $security->searchRoles($filter, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Util\RolesSearchResult', $searchResult);
        $this->assertEquals(2, $searchResult->getTotal());

        $profiles = $searchResult->getRoles();
        $this->assertInstanceOf('Kuzzle\Security\Role', $profiles[0]);
        $this->assertAttributeEquals('test', 'id', $profiles[0]);
        $this->assertAttributeEquals('test1', 'id', $profiles[1]);
    }

    function testSearchUsers()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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
                'body' => $filter
            ],
            'query_parameters' => []
        ];
        $advancedSearchResponse = [
            'hits' => [
                0 => [
                    '_id' => 'test',
                    '_source' => [
                        'foo' => 'bar',
                        'profile' => 'default'
                    ],
                    '_meta' => [],
                ],
                1 => [
                    '_id' => 'test1',
                    '_source' => [
                        'foo' => 'bar',
                        'profile' => 'default'
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $searchResult = $security->searchUsers($filter, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Util\UsersSearchResult', $searchResult);
        $this->assertEquals(2, $searchResult->getTotal());

        $profiles = $searchResult->getUsers();
        $this->assertInstanceOf('Kuzzle\Security\User', $profiles[0]);
        $this->assertAttributeEquals('test', 'id', $profiles[0]);
        $this->assertAttributeEquals('test1', 'id', $profiles[1]);
    }



    function testUpdateProfile()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $profileId = uniqid();
        $policiesBase = [
            [
                'roleId' => 'anonymous',
                'restrictedTo' => [
                    ['index' => 'my-second-index', 'collection' => ['my-collection']]
                ]
            ]
        ];
        $policies = [
            [
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
                'body' => [ 'policies' => $policies ]
            ],
            'query_parameters' => []
        ];
        $updateResponse = [
            '_id' => $profileId,
            '_source' => [ 'policies' => array_merge($policiesBase, $policies) ],
            '_meta' => [],
            '_version' => 1
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->updateProfile($profileId, $policies, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Profile', $result);
        $this->assertAttributeEquals($profileId, 'id', $result);
        $this->assertAttributeEquals(['policies' => array_merge($policiesBase, $policies)], 'content', $result);
    }

    function testUpdateRole()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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
                'body' => $roleUpdateContent
            ],
            'query_parameters' => []
        ];
        $updateResponse = [
            '_id' => $roleId,
            '_source' => array_merge($roleContent, $roleUpdateContent),
            '_meta' => [],
            '_version' => 1
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->updateRole($roleId, $roleUpdateContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Role', $result);
        $this->assertAttributeEquals($roleId, 'id', $result);
        $this->assertAttributeEquals(array_merge($roleContent, $roleUpdateContent), 'content', $result);
    }

    function testUpdateUser()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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
                'body' => $userContent
            ],
            'query_parameters' => []
        ];
        $updateResponse = [
            '_id' => $userId,
            '_source' => array_merge($userBaseContent, $userContent),
            '_meta' => [],
            '_version' => 1
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

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);

        $result = $security->updateUser($userId, $userContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\User', $result);
        $this->assertAttributeEquals($userId, 'id', $result);
        $this->assertAttributeEquals(array_merge($userBaseContent, $userContent), 'content', $result);
    }

    public function testCreateCredentials()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

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
                'body' => ['foo' => 'bar']
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

        $security = new Security($kuzzle);

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $security->createCredentials("local", "42", ["foo"=>"bar"], $options);
    }

    public function testDeleteCredentials()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

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
                'requestId' => $options['requestId']
            ],
            'method' => 'DELETE',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => [
                "acknowledged" => true,
            ]
        ];

        $security = new Security($kuzzle);

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $security->deleteCredentials("local", "42", $options);
    }

    public function testGetAllCredentialFields()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

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

        $security = new Security($kuzzle);

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $security->getAllCredentialFields($options);
    }

    public function testGetCredentialFields()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

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

        $security = new Security($kuzzle);

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $security->getCredentialFields('local', $options);
    }

    public function testGetCredentials()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

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

        $security = new Security($kuzzle);

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $security->getCredentials('local', '42', $options);
    }

    public function testGetCredentialsById()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

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

        $security = new Security($kuzzle);

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $security->getCredentialsById('local', '42', $options);
    }

    public function testHasCredentials()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

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

        $security = new Security($kuzzle);

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $security->hasCredentials('local', '42', $options);
    }

    public function testUpdateCredentials()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

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
                'body' => ["foo" => "bar"],
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

        $security = new Security($kuzzle);

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $security->updateCredentials('local', '42', ["foo" => "bar"], $options);
    }

    public function testValidateCredentials()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

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
                'body' => ["foo" => "bar"],
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => []
        ];

        $httpResponse = [
            "result" => true
        ];

        $security = new Security($kuzzle);

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $security->validateCredentials('local', '42', ["foo" => "bar"], $options);
    }
}
