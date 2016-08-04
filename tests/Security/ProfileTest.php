<?php

use Kuzzle\Kuzzle;
use Kuzzle\Security\Policy;
use Kuzzle\Security\Profile;
use Kuzzle\Security\Role;
use Kuzzle\Security\Security;

class ProfileTest extends \PHPUnit_Framework_TestCase
{
    function testSave()
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

        $profileAdditionalPolicy = [
            'roleId' => 'admin',
            'restrictedTo' => [
                ['index' => 'my-admin-index'],
            ],
            'allowInternalIndex'=> false
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
                'body' => array_merge_recursive($profileContent, ['policies' => [$profileAdditionalPolicy]])
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
        $profile = new Profile($security, $profileId, $profileContent);

        $additionalPolicy = new Policy($profile, $profileAdditionalPolicy['roleId']);
        $additionalPolicy->setRestrictedTo($profileAdditionalPolicy['restrictedTo']);
        $additionalPolicy->setAllowInternalIndex($profileAdditionalPolicy['allowInternalIndex']);

        $profile->addPolicy($additionalPolicy);

        $result = $profile->save(['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Profile', $result);
        $this->assertAttributeEquals($profileId, 'id', $result);
        $this->assertAttributeEquals(array_merge_recursive($profileContent, ['policies' => [$profileAdditionalPolicy]]), 'content', $result);
    }

    function testUpdate()
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
            'route' => '/api/1.0/profiles/' . $profileId,
            'method' => 'POST',
            'request' => [
                'metadata' => [],
                'controller' => 'security',
                'action' => 'updateProfile',
                'requestId' => $requestId,
                '_id' => $profileId,
                'body' => $profileContent
            ]
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
        $profile = new Profile($security, $profileId);

        $result = $profile->update($profileContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Security\Profile', $result);
        $this->assertAttributeEquals($profileId, 'id', $result);
        $this->assertAttributeEquals(array_merge($profileBaseContent, $profileContent), 'content', $result);
    }

    function testDelete()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
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
        $profile = new Profile($security, $profileId, []);

        $result = $profile->delete(['requestId' => $requestId]);

        $this->assertEquals($profileId, $result);
    }


    function testPolicies()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;

        $profileId = uniqid();
        $roleId = uniqid();

        $policyDescription = [
            'roleId' => 'admin',
            'restrictedTo' => [
                ['index' => 'my-admin-index'],
            ],
            'allowInternalIndex'=> false
        ];

        $kuzzle = new Kuzzle($url);
        $security = new Security($kuzzle);
        $profile = new Profile($security, $profileId);
        $role = new Role($security, $roleId);

        try {
            $profile->setPolicies(['admin']);

            $this->fail('ProfileTest::testPolicies => Should raise an exception');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Unable to extract policy from description: an instance of \Kuzzle\Security\Policy or a array is required', $e->getMessage());
        }

        // test set policies with policy description
        $profile->setPolicies([$policyDescription]);
        $policies = $profile->getPolicies();

        $this->assertEquals(1, count($policies));
        $this->assertInstanceOf('\Kuzzle\Security\Policy', $policies[0]);
        $this->assertAttributeEquals($policyDescription['roleId'], 'roleId', $policies[0]);
        $this->assertAttributeEquals($policyDescription['restrictedTo'], 'restrictedTo', $policies[0]);
        $this->assertAttributeEquals($policyDescription['allowInternalIndex'], 'allowInternalIndex', $policies[0]);

        // test setting policies from set content
        $profile->setContent(['policies' => [$policyDescription]]);
        $policies = $profile->getPolicies();

        $this->assertEquals(1, count($policies));
        $this->assertInstanceOf('\Kuzzle\Security\Policy', $policies[0]);
        $this->assertAttributeEquals($policyDescription['roleId'], 'roleId', $policies[0]);
        $this->assertAttributeEquals($policyDescription['restrictedTo'], 'restrictedTo', $policies[0]);
        $this->assertAttributeEquals($policyDescription['allowInternalIndex'], 'allowInternalIndex', $policies[0]);

        // test altering policy directly from object
        $policies[0]->setRole($role);
        $policies[0]->addRestriction(['index' => 'foo-bar']);

        $this->assertAttributeEquals($roleId, 'roleId', $policies[0]);
        $this->assertAttributeEquals(array_merge($policyDescription['restrictedTo'], [['index' => 'foo-bar']]), 'restrictedTo', $policies[0]);
    }
}
