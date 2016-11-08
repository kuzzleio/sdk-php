<?php

namespace Kuzzle\Util;

use Kuzzle\Document;

class AdvancedSearchResult
{
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
     * AdvancedSearchResult constructor.
     *
     * @param integer $total
     * @param Document[] $documents
     * @param array $aggregations
     */
    public function __construct($total, array $documents, array $aggregations = [])
    {
        $this->total = $total;
        $this->documents = $documents;
        $this->aggregations = $aggregations;

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
}
