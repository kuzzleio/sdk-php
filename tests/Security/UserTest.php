<?php

use Kuzzle\Kuzzle;
use Kuzzle\Security\User;
use Kuzzle\Security\Profile;
use Kuzzle\Security\Security;

class UserTest extends \PHPUnit_Framework_TestCase
{
    function testEmptyGetProfiles()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $security = new Security($kuzzle);
        $user = new User($security, '', []);

        $this->assertEquals($user->getProfiles(), []);
    }

    function testGetProfiles()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $stubSecurity = $this
            ->getMockBuilder('Kuzzle\Security\Security')
            ->setConstructorArgs([$kuzzle])
            ->getMock();
        $stubSecurity->method('fetchProfile')->willReturn(new Profile($stubSecurity, 'foo', []));

        $user = new User($stubSecurity, 'foobar', [
            'profileIds' => ['foo', 'bar', 'baz']
        ]);

        $profiles = $user->getProfiles();
        $this->assertEquals(3, count($profiles));

        foreach($profiles as $profile) {
            $this->assertInstanceOf('Kuzzle\Security\Profile', $profile);
            $this->assertEquals('foo', $profile->getId());
        }
    }

    function testEmptyGetProfileIds()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $security = new Security($kuzzle);
        $user = new User($security, '', []);

        $this->assertEquals($user->getProfileIds(), []);
    }

    function testGetProfileIds()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $security = new Security($kuzzle);
        $user = new User($security, 'foobar', [
            'profileIds' => ['foo', 'bar', 'baz']
        ]);

        $this->assertEquals($user->getProfileIds(), ['foo', 'bar', 'baz']);
    }

    function testAddProfile()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $security = new Security($kuzzle);
        $user = new User($security, '', []);

        $this->assertEquals($user, $user->addProfile('myProfile'));
    }

    function testCreate()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $userId = uniqid();
        $userContent = [
            'profileIds' => ['admin']
        ];
        $userCredentials = [
            'some' => 'credentials'
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
                'body' => [
                    'content' => $userContent,
                    'credentials' => $userCredentials
                ]
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
            ->expects($this->exactly(1))
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);
        $user = new User($security, $userId);

        $user->setContent($userContent);
        $user->setCredentials($userCredentials);
        $profile = new Profile($security, $userContent['profileIds'][0]);
        $user->setProfiles([$profile]);
        $result = $user->create(['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\User', $result);
        $this->assertAttributeEquals($userId, 'id', $result);
        $this->assertAttributeEquals($userContent, 'content', $result);
    }

    function testReplace()
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
            ->expects($this->exactly(1))
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $security = new Security($kuzzle);
        $user = new User($security, $userId);

        $profile = new Profile($security, $userContent['profileIds'][0]);
        $user->setProfiles([$profile]);
        $user->setContent($userContent);
        $result = $user->replace(['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\User', $result);
        $this->assertAttributeEquals($userId, 'id', $result);
        $this->assertAttributeEquals($userContent, 'content', $result);
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
        $user = new User($security, $userId);

        $result = $user->update($userContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\User', $result);
        $this->assertAttributeEquals($userId, 'id', $result);
        $this->assertAttributeEquals(array_merge($userBaseContent, $userContent), 'content', $result);
    }

    function testDelete()
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
        $user = new User($security, $userId, []);

        $result = $user->delete(['requestId' => $requestId]);

        $this->assertEquals($userId, $result);
    }
}
