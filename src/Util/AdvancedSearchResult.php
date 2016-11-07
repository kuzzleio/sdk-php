<?php

namespace Kuzzle\Util;

use InvalidArgumentException;
use Kuzzle\DataCollection;
use Kuzzle\Document;

class AdvancedSearchResult
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
     * @var AdvancedSearchResult
     */
    private $previous = null;

    /**
     * AdvancedSearchResult constructor.
     *
     * @param DataCollection $dataCollection
     * @param integer $total
     * @param Document[] $documents
     * @param array $searchArgs
     * @param AdvancedSearchResult $previous
     */
    public function __construct(DataCollection $dataCollection, $total, array $documents, array $searchArgs = [], AdvancedSearchResult $previous = null)
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
     * @return AdvancedSearchResult
     */
    public function getNext()
    {
        if (array_key_exists('scrollId', $this->searchArgs['options'])) {
            $options = $this->searchArgs['options'];

            if (array_key_exists('scroll', $this->searchArgs['filters'])) {
                $options['scroll'] = $this->searchArgs['filters']['scroll'];
            }

            $response = $this->dataCollection->getKuzzle()->scroll(
                $options['scrollId'],
                $options
            );

            $fetchedDocuments = count($this->documents);
            $previous = $this;

            while ($previous = $previous->getPrevious()) {
                $fetchedDocuments += count($previous->getDocuments());
            }

            if ($fetchedDocuments >= $this->getTotal()) {
                return null;
            }

            $response['result']['hits'] = array_map(function ($document) {
                return new Document($this->dataCollection, $document['_id'], $document['_source']);
            }, $response['result']['hits']);

            if (array_key_exists('_scroll_id', $response['result'])) {
                $options['scrollId'] = $response['result']['_scroll_id'];
            }

            return new AdvancedSearchResult($this->dataCollection, $response['result']['total'], $response['result']['hits'], ['options' => $options, 'filters' => $this->searchArgs['filters']], $this);
        } else if (array_key_exists('from', $this->searchArgs['filters']) && array_key_exists('size', $this->searchArgs['filters'])) {
            $filters = $this->searchArgs['filters'];
            $filters['from'] += $filters['size'];

            if ($filters['from'] >= $this->getTotal()) {
                return null;
            }

            $searchResult = $this->dataCollection->search($filters, $this->searchArgs['options']);
            $searchResult->setPrevious($this);

            return $searchResult;
        }

        throw new InvalidArgumentException('Unable to retrieve next results from search: missing scrollId or from/size params');
    }

    /**
     * @return AdvancedSearchResult
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * @param AdvancedSearchResult $previous
     *
     * @return $this
     */
    public function setPrevious(AdvancedSearchResult $previous)
    {
        $this->previous = $previous;

        return $this;
    }
}
