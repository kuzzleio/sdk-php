<?php

use Kuzzle\Kuzzle;
use Kuzzle\Security\Role;
use Kuzzle\Security\Security;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    function testSave()
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
        $role = new Role($security, $roleId);

        $role->setContent($roleContent);
        $result = $role->save(['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Role', $result);
        $this->assertAttributeEquals($roleId, 'id', $result);
        $this->assertAttributeEquals($roleContent, 'content', $result);
    }

    function testUpdate()
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
        $role = new Role($security, $roleId);

        $role->setContent($roleContent);
        $result = $role->update($roleUpdateContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Role', $result);
        $this->assertAttributeEquals($roleId, 'id', $result);
        $this->assertAttributeEquals(array_merge($roleContent, $roleUpdateContent), 'content', $result);
    }

    function testDelete()
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
        $role = new Role($security, $roleId, []);

        $result = $role->delete(['requestId' => $requestId]);

        $this->assertEquals($roleId, $result);
    }

    function testGetMeta()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $metas = [
            'createdAt' => '0123456789',
            'author' => '-1'
        ];

        $security = new Security($kuzzle);
        $role = new Role($security, 'foobar', [], $metas);

        $this->assertEquals($role->getMeta(), $metas);
    }
}
