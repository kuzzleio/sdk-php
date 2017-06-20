<?php

use Kuzzle\Document;
use Kuzzle\Collection;
use Kuzzle\Util\SearchResult;

class SearchResultTestTest extends \PHPUnit_Framework_TestCase
{
    function testSearchResultMethods()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $index = 'index';
        $collection = 'collection';

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $dataCollection = new Collection($kuzzle, $collection, $index);

        $firstDocumentId = uniqid();
        $firstDocumentContent = [
            'name' => 'John',
            'age' => 42
        ];

        $secondDocumentId = uniqid();
        $secondDocumentContent = [
            'name' => 'Michael',
            'age' => 36
        ];

        $documents = [
            new Document($dataCollection, $firstDocumentId, $firstDocumentContent),
            new Document($dataCollection, $secondDocumentId, $secondDocumentContent)
        ];

        $options = ['from' => 0, 'size' => 1];
        $filters = ['sort' => [['age' => 'desc']]];

        $searchResult = new SearchResult($dataCollection, 42, $documents, [], $options, $filters);

        $this->assertEquals($searchResult->getTotal(), 42);
        $this->assertEquals($searchResult->getDocuments(), $documents);
        $this->assertEquals($searchResult->getOptions(), $options);
        $this->assertEquals($searchResult->getFilters(), $filters);
        $this->assertEquals($searchResult->getCollection(), $dataCollection);
        $this->assertEquals($searchResult->getFetchedDocuments(), 2);
    }

    function testFetchNextWithSearchAfter()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $dataCollection = new Collection($kuzzle, $collection, $index);

        $firstDocumentId = uniqid();
        $firstDocumentContent = [
            'name' => 'John',
            'age' => 42
        ];

        $secondDocumentId = uniqid();
        $secondDocumentContent = [
            'name' => 'Michael',
            'age' => 36
        ];

        $firstDocument = new Document($dataCollection, $firstDocumentId, $firstDocumentContent);
        $secondDocument = new Document($dataCollection, $secondDocumentId, $secondDocumentContent);

        $documents = [$firstDocument];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_search',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'search',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                'body' => ['sort' => [['age' => 'desc']], 'search_after' => [42]]
            ],
            'query_parameters' => ['size' => 1]
        ];

        $searchResponse = [
            'hits' => [
                0 => [
                    '_id' => $secondDocumentId,
                    '_source' => $secondDocumentContent,
                    '_meta' => []
                ]
            ],
            'total' => 42
        ];
        $httpResponse = [
            'error' => null,
            'result' => $searchResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $options = ['size' => 1, 'requestId' => $requestId];
        $filters = ['sort' => [['age' => 'desc']]];

        $initialSearchResult = new SearchResult($dataCollection, 42, $documents, [], $options, $filters);

        $result = $initialSearchResult->fetchNext();

        $secondSearchResult = new SearchResult($dataCollection, 42, [$secondDocument], [], $options, $filters);

        $this->assertEquals($secondSearchResult->getDocuments(), $result->getDocuments());
    }

    function testFetchNextWithScroll()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $dataCollection = new Collection($kuzzle, $collection, $index);

        $firstDocumentId = uniqid();
        $firstDocumentContent = [
            'name' => 'John',
            'age' => 42
        ];

        $secondDocumentId = uniqid();
        $secondDocumentContent = [
            'name' => 'Michael',
            'age' => 36
        ];

        $firstDocument = new Document($dataCollection, $firstDocumentId, $firstDocumentContent);
        $secondDocument = new Document($dataCollection, $secondDocumentId, $secondDocumentContent);

        $documents = [$firstDocument];

        $scrollId = uniqid();

        $httpRequest = [
            'route' => '/_scroll/' . $scrollId,
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'scroll',
                'requestId' => $requestId
            ],
            'query_parameters' => []
        ];

        $searchResponse = [
            'hits' => [
                0 => [
                    '_id' => $secondDocumentId,
                    '_source' => $secondDocumentContent,
                    '_meta' => []
                ]
            ],
            'total' => 42
        ];
        $httpResponse = [
            'error' => null,
            'result' => $searchResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $options = ['from' => 0, 'size' => 1, 'scrollId' => $scrollId, 'requestId' => $requestId];
        $filters = ['sort' => [['age' => 'desc']]];

        $initialSearchResult = new SearchResult($dataCollection, 42, $documents, [], $options, $filters);

        $result = $initialSearchResult->fetchNext();

        $secondSearchResult = new SearchResult($dataCollection, 42, [$secondDocument], [], $options, $filters);

        $this->assertEquals($secondSearchResult->getDocuments(), $result->getDocuments());
    }

    function testFetchNextWithSearchFromSize()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $dataCollection = new Collection($kuzzle, $collection, $index);

        $firstDocumentId = uniqid();
        $firstDocumentContent = [
            'name' => 'John',
            'age' => 42
        ];

        $secondDocumentId = uniqid();
        $secondDocumentContent = [
            'name' => 'Michael',
            'age' => 36
        ];

        $firstDocument = new Document($dataCollection, $firstDocumentId, $firstDocumentContent);
        $secondDocument = new Document($dataCollection, $secondDocumentId, $secondDocumentContent);

        $documents = [$firstDocument];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_search',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'search',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                'body' => (object)[]
            ],
            'query_parameters' => ['from' => 1, 'size' => 1]
        ];

        $searchResponse = [
            'hits' => [
                0 => [
                    '_id' => $secondDocumentId,
                    '_source' => $secondDocumentContent,
                    '_meta' => []
                ]
            ],
            'total' => 42
        ];
        $httpResponse = [
            'error' => null,
            'result' => $searchResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $options = ['from' => 0, 'size' => 1, 'requestId' => $requestId];
        $filters = [];

        $initialSearchResult = new SearchResult($dataCollection, 42, $documents, [], $options, $filters);

        $result = $initialSearchResult->fetchNext();

        $secondSearchResult = new SearchResult($dataCollection, 42, [$secondDocument], [], $options, $filters);

        $this->assertEquals($secondSearchResult->getDocuments(), $result->getDocuments());
    }
}
