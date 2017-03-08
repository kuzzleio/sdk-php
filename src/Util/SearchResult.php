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
    private $options = [];

    /**
     * @var array
     */
    private $filters = [];

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
     * @param array $options
     * @param array $fiters
     * @param SearchResult $previous
     * @internal param array $searchArgs
     */
    public function __construct(Collection $collection, $total, array $documents, array $aggregations = [], array $options = [], array $fiters = [], SearchResult $previous = null)
    {
        $this->collection = $collection;
        $this->total = $total;
        $this->documents = $documents;
        $this->aggregations = $aggregations;
        $this->options = $options;
        $this->filters = $fiters;
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
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
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

        if (array_key_exists('scrollId', $this->options) && array_key_exists('scroll', $this->options)) {
            // retrieve next results with scroll if original search use it
            if ($this->fetchedDocuments >= $this->getTotal()) {
                return null;
            }

            $options = $this->options;
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
                $this->filters
            );
        } else if (array_key_exists('from', $this->options) && array_key_exists('size', $this->options)) {
            // retrieve next results with  from/size if original search use it
            $filters = $this->filters;
            $options = $this->options;
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
