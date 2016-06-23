<?php

namespace Kuzzle;

/**
 * Class DataMapping
 * @package kuzzle-sdk
 */
class DataMapping
{
    /**
     * @var array Common headers for all sent documents.
     */
    protected $mapping = [];

    /**
     * @var array Easy-to-understand list of mappings per field
     */
    protected $headers = [];

    /**
     * DataMapping constructor.
     *
     * @param DataCollection $kuzzleDataCollection An instantiated Kuzzle\DataCollection object
     * @param array $mapping Optional mapping
     */
    public function __construct(DataCollection $kuzzleDataCollection, array $mapping = [])
    {


        return $this;
    }

    /**
     * Applies the new mapping to the data collection.
     *
     * @param array $options Optional parameters
     * @return DataMapping
     */
    public function apply(array $options = [])
    {

        return $this;
    }

    /**
     * Instanciates a new KuzzleDataMapping object with an up-to-date content.
     *
     * @param array $options Optional parameters
     * @return DataMapping
     */
    public function refresh(array $options = [])
    {

        return $this;
    }

    /**
     * Adds or updates a field mapping.
     *
     * @param string $field Name of the field from which the mapping is to be added or updated
     * @param array $mapping Mapping for this field, following the Elasticsearch Mapping format
     * @return DataMapping
     */
    public function set($field, array $mapping)
    {

        return $this;
    }

    /**
     * This is a helper function returning itself, allowing to easily chain calls.
     *
     * @param array $content New content
     * @param bool $replace true: replace the current content with the provided data, false: merge it
     * @return DataMapping
     */
    public function setHeaders(array $content, $replace = false)
    {

        return $this;
    }
}