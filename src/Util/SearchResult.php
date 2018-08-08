<?php

namespace Kuzzle\Util;

use InvalidArgumentException;

/**
 * Class SearchResult
 * @package Kuzzle\Util
 */
class SearchResult
{
    /**
     * @var string
     */
    private $collection = '';

    /**
     * @var integer
     */
    private $total = 0;

    /**
     * @var array[]
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
     * @param string $collection
     * @param integer $total
     * @param array[] $documents
     * @param array $aggregations
     * @param array $options
     * @param array $filters
     * @param SearchResult $previous
     * @internal param array $searchArgs
     */
    public function __construct($collection, $total, array $documents, array $aggregations = [], array $options = [], array $filters = [], SearchResult $previous = null)
    {
        $this->collection = $collection;
        $this->total = $total;
        $this->documents = $documents;
        $this->aggregations = $aggregations;
        $this->options = $options;
        $this->filters = $filters;
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
     * @return array[]
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @return string
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
}
