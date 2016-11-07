<?php

namespace Kuzzle\Util;

use InvalidArgumentException;
use Kuzzle\DataCollection;
use Kuzzle\Document;

/**
 * Class SearchResult
 * @package Kuzzle\Util
 *
 * @todo: Implement Iterator interface
 */
class SearchResult
{
    /**
     * @var DataCollection
     */
    private $dataCollection = null;

    /**
     * @var integer
     */
    private $total = 0;

    /**
     * @var Document[]
     */
    private $documents = [];

    /**
     * @var array
     */
    private $searchArgs = [];

    /**
     * @var SearchResult
     */
    private $previous = null;

    /**
     * @var SearchResult
     */
    private $next = null;

    /**
     * SearchResult constructor.
     *
     * @param DataCollection $dataCollection
     * @param integer $total
     * @param Document[] $documents
     * @param array $searchArgs
     * @param SearchResult $previous
     */
    public function __construct(DataCollection $dataCollection, $total, array $documents, array $searchArgs = [], SearchResult $previous = null)
    {
        $this->dataCollection = $dataCollection;
        $this->total = $total;
        $this->documents = $documents;
        $this->searchArgs = $searchArgs;
        $this->previous = $previous;

        return $this;
    }

    /**
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return Document[]
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @return SearchResult
     */
    public function getNext()
    {
        if (!$this->next) {
            if (array_key_exists('scrollId', $this->searchArgs['options'])) {
                // retrieve next results with scroll if original search use it
                $options = $this->searchArgs['options'];

                if (array_key_exists('scroll', $this->searchArgs['filters'])) {
                    $options['scroll'] = $this->searchArgs['filters']['scroll'];
                }

                // check if we need to scroll again to fetch all matching documents
                $fetchedDocuments = count($this->documents);
                $previous = $this;

                while ($previous = $previous->getPrevious()) {
                    $fetchedDocuments += count($previous->getDocuments());
                }

                if ($fetchedDocuments >= $this->getTotal()) {
                    return null;
                }

                $response = $this->dataCollection->getKuzzle()->scroll(
                    $options['scrollId'],
                    $options
                );

                // hydrate documents
                $response['result']['hits'] = array_map(function ($document) {
                    return new Document($this->dataCollection, $document['_id'], $document['_source']);
                }, $response['result']['hits']);

                if (array_key_exists('_scroll_id', $response['result'])) {
                    $options['scrollId'] = $response['result']['_scroll_id'];
                }

                $this->next = new SearchResult($this->dataCollection, $response['result']['total'], $response['result']['hits'], ['options' => $options, 'filters' => $this->searchArgs['filters']], $this);
            } else if (array_key_exists('from', $this->searchArgs['filters']) && array_key_exists('size', $this->searchArgs['filters'])) {
                // retrieve next results with  from/size if original search use it
                $filters = $this->searchArgs['filters'];
                $filters['from'] += $filters['size'];

                // check if we need to do next request to fetch all matching documents
                if ($filters['from'] >= $this->getTotal()) {
                    return null;
                }

                $searchResult = $this->dataCollection->search($filters, $this->searchArgs['options']);
                $searchResult->setPrevious($this);

                $this->next = $searchResult;
            }
        }

        if ($this->next instanceof SearchResult) {
            return $this->next;
        }

        throw new InvalidArgumentException('Unable to retrieve next results from search: missing scrollId or from/size params');
    }

    /**
     * @return SearchResult
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * @param SearchResult $previous
     *
     * @return $this
     */
    public function setPrevious(SearchResult $previous)
    {
        $this->previous = $previous;

        return $this;
    }

    /**
     * @param SearchResult $next
     *
     * @return $this
     */
    public function setNext(SearchResult $next)
    {
        $this->next = $next;

        return $this;
    }
}
