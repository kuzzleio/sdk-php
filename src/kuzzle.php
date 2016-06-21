<?php

namespace Kuzzle;

use Kuzzle\DataCollection;
use Kuzzle\Security\Security;
use Kuzzle\Security\User;

/**
 * Class Kuzzle
 * @package kuzzle-sdk
 */
class Kuzzle {

    /**
     * @var string Kuzzle’s default index to use
     */
    protected $defaultIndex;

    /**
     * @var array Common headers for all sent documents.
     */
    protected $headers = [];

    /**
     * @var array Common metadata, will be sent to all future requests
     */
    protected $metadata = [];

    /**
     * @var string Token used in requests for authentication.
     */
    protected $jwtToken;

    /**
     * @var DataCollection[]
     */
    protected $collections = [];


    /**
     * Kuzzle constructor.
     *
     * @param string $url URL to the target Kuzzle instance
     * @param array $options Optional Kuzzle connection configuration
     * @return Kuzzle
     */
    function __construct($url, array $options = array())
    {

        return $this;
    }

    /**
     * Adds a listener to a Kuzzle global event.
     * When an event is fired, listeners are called in the order of their insertion.
     *
     * @param string $event One of the event described in the Event Handling section of the kuzzle documentation
     * @param callable $listener The function to call each time one of the registered event is fired
     * @return string containing an unique listener ID.
     */
    function addListener($event, $listener)
    {

    }

    /**
     * Checks the validity of a JSON Web Token.
     *
     * @param string $token The token to check
     * @return array with a valid boolean property.
     *         If the token is valid, a expiresAt property is set with the expiration timestamp.
     *         If not, a state property is set explaining why the token is invalid.
     */
    function checkToken($token)
    {

    }

    /**
     * Instantiates a new KuzzleDataCollection object.
     *
     * @param string $collection The name of the data collection you want to manipulate
     * @param string $index The name of the index containing the data collection
     * @return DataCollection
     *
     * @throws \InvalidArgumentException
     */
    function dataCollectionFactory($collection, $index = "")
    {
        if (empty($index))
        {
            if (empty($this->defaultIndex))
            {
                throw new \InvalidArgumentException('Unable to create a new data collection object: no index specified');
            }

            $index = $this->defaultIndex;
        }

        if (!$this->collections[$index])
        {
            $this->collections[$index] = [];
        }

        if (!$this->collections[$index][$collection])
        {
            $this->collections[$index][$collection] = new DataCollection($this, $index, $collection);
        }

        return $this->collections[$index][$collection];
    }

    /**
     * Kuzzle monitors active connections, and ongoing/completed/failed requests.
     * This method returns all available statistics from Kuzzle.
     *
     * @param array $options Optional parameters
     * @return array[] each one of them being a statistic frame
     */
    function getAllStatistics(array $options = [])
    {

    }

    /**
     * Get internal jwtToken used to request kuzzle.
     *
     * @return string
     */
    function getJwtToken()
    {
        return $this->jwtToken;
    }

    /**
     * Gets the rights of the current user
     *
     * @param array $options Optional parameters
     * @return array[]
     */
    function getMyRights(array $options = [])
    {

    }

    /**
     * Retrieves information about Kuzzle, its plugins and active services.
     *
     * @param array $options Optional parameters
     * @return array containing server information
     */
    function getServerInfo(array $options = [])
    {

    }

    /**
     * Kuzzle monitors active connections, and ongoing/completed/failed requests.
     * This method allows getting either the last statistics frame,
     * or a set of frames starting from a provided timestamp.
     *
     * @param string $timestamp Optional starting time from which the frames are to be retrieved
     * @param array $options Optional parameters
     * @return array[] containing one or more statistics frame(s)
     */
    function getStatistics($timestamp = '', array $options = [])
    {

    }

    /**
     * Retrieves the list of known data collections contained in a specified index.
     *
     * @param string $index Index containing the collections to be listed
     * @param array $options Optional parameters
     * @return array containing the list of stored and/or realtime collections on the provided index
     */
    function listCollections($index = '', array $options = [])
    {

    }

    /**
     * Retrieves the list of indexes stored in Kuzzle.
     *
     * @param array $options Optional parameters
     * @return array of index names
     */
    function listIndexes(array $options = [])
    {

    }

    /**
     * Log a user according to a strategy and credentials
     *
     * @param string $strategy Authentication strategy (local, facebook, github, …)
     * @param array $credentials Optional login credentials, depending on the strategy
     * @param string $expiresIn Login expiration time
     */
    function login($strategy, array $credentials = [], $expiresIn = '')
    {

    }

    /**
     * Logs the user out.
     */
    function logout()
    {

    }

    /**
     * Retrieves the current Kuzzle time.
     *
     * @param array $options Optional parameters
     */
    function now(array $options = [])
    {

    }

    /**
     * Base method used to send queries to Kuzzle
     *
     * @param array $queryArgs Query base arguments
     * @param array $query Query to execute
     * @param array $options Optional parameters
     */
    function query(array $queryArgs, array $query, array $options = [])
    {

    }

    /**
     * Given an index, the refresh action forces a refresh, on it,
     * making the documents visible to search immediately.
     *
     * @param string $index Optional. The index to refresh. If not set, defaults to Kuzzle->defaultIndex.
     * @param array $options Optional parameters
     * @return array structure matching the response from Elasticsearch
     */
    function refreshIndex($index = '', array $options = [])
    {

    }

    /**
     * @param string $event One of the event described in the Event Handling section of the kuzzle documentation
     */
    function removeAllListeners($event = '')
    {

    }

    /**
     * @param string $event One of the event described in the Event Handling section of the kuzzle documentation
     * @param string $listenerID The ID returned by Kuzzle->addListener()
     */
    function removeListener($event, $listenerID)
    {

    }

    /**
     * A static KuzzleSecurity instance
     *
     * @return Security
     */
    function security()
    {
        static $security;

        if (is_null($security))
        {
            $security = new Security($this);
        }

        return $security;
    }

    /**
     * The autoRefresh flag, when set to true,
     * will make Kuzzle perform a refresh request immediately after each write request,
     * forcing the documents to be immediately visible to search
     *
     * @param string $index Optional The index to set the autoRefresh for. If not set, defaults to Kuzzle->defaultIndex
     * @param bool $autoRefresh The value to set for the autoRefresh setting.
     * @param array $options Optional parameters
     */
    function setAutoRefresh($index = '', $autoRefresh = false, $options = [])
    {

    }

    /**
     * Set the default data index. Has the same effect than the defaultIndex constructor option.
     *
     * @param $index
     */
    function setDefaultIndex($index)
    {

    }

    /**
     * Performs a partial update on the current user.
     *
     * @param array $content
     * @param array $options
     */
    function updateSelf(array $content, $options = [])
    {

    }

    /**
     * This is a helper function returning itself, allowing to easily chain calls.
     *
     * @param array $headers
     * @param bool $replace
     * @return Kuzzle
     */
    function setHeaders(array $headers, $replace = false)
    {

        return $this;
    }

    /**
     * Sets the internal JWT token which will be used to request kuzzle.
     *
     * @param string $jwtToken Previously generated JSON Web Token
     * @return Kuzzle
     */
    function setJwtToken($jwtToken)
    {

        return $this;
    }

    /**
     * Retrieves current user object.
     *
     * @return User
     */
    function whoAmI()
    {

    }
}