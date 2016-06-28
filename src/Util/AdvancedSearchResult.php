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
     * AdvancedSearchResult constructor.
     *
     * @param integer $total
     * @param Document[] $documents
     */
    public function __construct($total, array $documents)
    {
        $this->total = $total;
        $this->documents = $documents;

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
}
