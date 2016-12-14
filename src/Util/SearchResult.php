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
    private $aggregations = [];

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
     * @param array $aggregations
     * @param array $searchArgs
     * @param SearchResult $previous
     */
    public function __construct(DataCollection $dataCollection, $total, array $documents, array $aggregations = [], array $searchArgs = [], SearchResult $previous = null)
    {
        $this->dataCollection = $dataCollection;
        $this->total = $total;
        $this->documents = $documents;
        $this->aggregations = $aggregations;
        $this->searchArgs = $searchArgs;
        $this->previous = $previous;

        if ($this->previous instanceof SearchResult) {
            $this->previous->setNext($this);
        }

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
     * @return array
     */
    public function getAggregations()
    {
        return $this->aggregations;
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

                $searchResult = $this->dataCollection->scroll(
                    $options['scrollId'],
                    $options,
                    $this->searchArgs['filters']
                );
                $searchResult->setPrevious($this);

                $this->next = $searchResult;
            } else if (array_key_exists('from', $this->searchArgs['options']) && array_key_exists('size', $this->searchArgs['options'])) {
                // retrieve next results with  from/size if original search use it
                $filters = $this->searchArgs['filters'];
                $this->searchArgs['options']['from'] += $this->searchArgs['options']['size'];

                // check if we need to do next request to fetch all matching documents
                if ($this->searchArgs['options']['from'] >= $this->getTotal()) {
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

        throw new InvalidArgumentException('Unable to retrieve next results from search: missing scrollId or from/size options');
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
