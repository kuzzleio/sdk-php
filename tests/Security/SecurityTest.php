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
            'route' => '/profiles/' . $profileId . '/_create',
            'method' => 'POST',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'createProfile',
                'requestId' => $requestId,
                '_id' => $profileId,
                'body' => $profileContent
            ],
            'query_parameters' => []
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
            'method' => 'PUT',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'createOrReplaceProfile',
                'requestId' => $requestId,
                '_id' => $profileId,
                'body' => $profileContent
            ],
            'query_parameters' => []
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
                'metadata' => [],
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
                'metadata' => [],
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
            'profileIds' => ['admin']
        ];

        $httpRequest = [
            'route' => '/users/' . $userId . '/_create',
            'method' => 'POST',
            'request' => [
                'metadata' => [],
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
                'metadata' => [],
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

    function testCreateOrReplaceUser()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $userId = uniqid();
        $userContent = [
            'profileIds' => ['admin']
        ];

        $httpRequest = [
            'route' => '/users/' . $userId,
            'method' => 'PUT',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'createOrReplaceUser',
                'requestId' => $requestId,
                '_id' => $userId,
                'body' => $userContent
            ],
            'query_parameters' => []
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
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $profileId = uniqid();

        $httpRequest = [
            'route' => '/profiles/' . $profileId,
            'method' => 'DELETE',
            'request' => [
                'metadata' => [],
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

    function testDeleteUser()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $userId = uniqid();

        $httpRequest = [
            'route' => '/users/' . $userId,
            'method' => 'DELETE',
            'request' => [
                'metadata' => [],
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

    function testDeleteRole()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $roleId = uniqid();

        $httpRequest = [
            'route' => '/roles/' . $roleId,
            'method' => 'DELETE',
            'request' => [
                'metadata' => [],
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
                'metadata' => [],
                'controller' => 'security',
                'action' => 'getProfile',
                'requestId' => $requestId,
                '_id' => $profileId,
            ],
            'query_parameters' => []
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
                'metadata' => [],
                'controller' => 'security',
                'action' => 'getRole',
                'requestId' => $requestId,
                '_id' => $roleId,
            ],
            'query_parameters' => []
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
                'metadata' => [],
                'controller' => 'security',
                'action' => 'getUser',
                'requestId' => $requestId,
                '_id' => $userId,
            ],
            'query_parameters' => []
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
                'metadata' => [],
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
                'metadata' => [],
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
                    ]
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
                    ]
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
                'metadata' => [],
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
                    ]
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
                    ]
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
                'metadata' => [],
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
                    ]
                ],
                1 => [
                    '_id' => 'test1',
                    '_source' => [
                        'foo' => 'bar',
                        'profile' => 'default'
                    ]
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
        $profileBaseContent = [
            'policies' => [
                [
                    'roleId' => 'anonymous',
                    'restrictedTo' => [
                        ['index' => 'my-second-index', 'collection' => ['my-collection']]
                    ]
                ]
            ]
        ];
        $profileContent = [
            'policies' => [
                [
                    'roleId' => 'default',
                    'restrictedTo' => [
                        ['index' => 'my-index'],
                        ['index' => 'my-second-index', 'collection' => ['my-collection']]
                    ],
                    'allowInternalIndex'=> false
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/profiles/' . $profileId . '/_update',
            'method' => 'PUT',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'updateProfile',
                'requestId' => $requestId,
                '_id' => $profileId,
                'body' => $profileContent
            ],
            'query_parameters' => []
        ];
        $updateResponse = [
            '_id' => $profileId,
            '_source' => array_merge($profileBaseContent, $profileContent),
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

        $result = $security->updateProfile($profileId, $profileContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Profile', $result);
        $this->assertAttributeEquals($profileId, 'id', $result);
        $this->assertAttributeEquals(array_merge($profileBaseContent, $profileContent), 'content', $result);
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
                'metadata' => [],
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

    function testUpdate()
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
                'metadata' => [],
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

}
