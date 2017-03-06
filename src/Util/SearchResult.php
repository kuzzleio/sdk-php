<?php

namespace Kuzzle\Util;

use InvalidArgumentException;
use Kuzzle\Collection;
use Kuzzle\Document;

/**
 * Class SearchResult
 * @package Kuzzle\Util
 */
class SearchResult
{
    /**
     * @var Collection
     */
    private $collection = null;

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
     * @var int
     */
    private $fetchedDocuments = 0;

    /**
     * SearchResult constructor.
     *
     * @param Collection $collection
     * @param integer $total
     * @param Document[] $documents
     * @param array $aggregations
     * @param array $searchArgs
     * @param SearchResult $previous
     */
    public function __construct(Collection $collection, $total, array $documents, array $aggregations = [], array $searchArgs = [], SearchResult $previous = null)
    {
        $this->collection = $collection;
        $this->total = $total;
        $this->documents = $documents;
        $this->aggregations = $aggregations;
        $this->searchArgs = $searchArgs;
        $this->fetchedDocuments = count($documents) + ($previous instanceof SearchResult ? $previous->fetchedDocuments : 0);

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
     * @return Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return array
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @return array
     */
    public function getSearchArgs()
    {
        return $this->searchArgs;
    }

    /**
     * @return int
     */
    public function getFetchedDocuments()
    {
        return $this->fetchedDocuments;
    }

    /**
     * @return SearchResult
     */
    public function fetchNext()
    {
        $searchResult = null;

        if (array_key_exists('scrollId', $this->searchArgs['options']) && array_key_exists('scroll', $this->searchArgs['options'])) {
            // retrieve next results with scroll if original search use it
            if ($this->fetchedDocuments >= $this->getTotal()) {
                return null;
            }

            $options = $this->searchArgs['options'];
            $options['previous'] = $this;

            if (array_key_exists('from', $options)) {
                unset($options['from']);
            }

            if (array_key_exists('size', $options)) {
                unset($options['size']);
            }

            $searchResult = $this->collection->scroll(
                $options['scrollId'],
                $options['scroll'],
                $options,
                $this->searchArgs['filters']
            );
        } else if (array_key_exists('from', $this->searchArgs['options']) && array_key_exists('size', $this->searchArgs['options'])) {
            // retrieve next results with  from/size if original search use it
            $filters = $this->searchArgs['filters'];
            $options = $this->searchArgs['options'];
            $options['previous'] = $this;

            $options['from'] += $options['size'];

            // check if we need to do next request to fetch all matching documents
            if ($options['from'] >= $this->getTotal()) {
                return null;
            }

            $searchResult = $this->collection->search($filters, $options);
        }

        if ($searchResult && $searchResult instanceof SearchResult) {
            return $searchResult;
        }

        throw new InvalidArgumentException('Unable to retrieve next results from search: missing scrollId or from/size options');
    }
}
