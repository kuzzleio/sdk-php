<?php

namespace Kuzzle;

use ErrorException;
use Exception;
use HttpException;
use InvalidArgumentException;
use Kuzzle\DataCollection;
use Kuzzle\Security\Security;
use Kuzzle\Security\User;
use Ramsey\Uuid\Uuid;

/**
 * Class Kuzzle
 * @package kuzzle-sdk
 */
class Kuzzle {

    /**
     * @var string url of kuzzle http server
     */
    protected $url;

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
     * @var array
     */
    private $routesDescription = [];


    /**
     * Kuzzle constructor.
     *
     * @param string $url URL to the target Kuzzle instance
     * @param array $options Optional Kuzzle connection configuration
     * @return Kuzzle
     */
    public function __construct($url, array $options = array())
    {
        $this->url = $url;

        $this->getRouteDescription();

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
    public function addListener($event, $listener)
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
    public function checkToken($token)
    {

    }

    /**
     * Instantiates a new KuzzleDataCollection object.
     *
     * @param string $collection The name of the data collection you want to manipulate
     * @param string $index The name of the index containing the data collection
     * @return DataCollection
     *
     * @throws InvalidArgumentException
     */
    public function dataCollectionFactory($collection, $index = "")
    {
        if (empty($index))
        {
            if (empty($this->defaultIndex))
            {
                throw new InvalidArgumentException('Unable to create a new data collection object: no index specified');
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
    public function getAllStatistics(array $options = [])
    {

    }

    /**
     * Get internal jwtToken used to request kuzzle.
     *
     * @return string
     */
    public function getJwtToken()
    {
        return $this->jwtToken;
    }

    /**
     * Gets the rights of the current user
     *
     * @param array $options Optional parameters
     * @return array[]
     */
    public function getMyRights(array $options = [])
    {

    }

    /**
     * Retrieves information about Kuzzle, its plugins and active services.
     *
     * @param array $options Optional parameters
     * @return array containing server information
     */
    public function getServerInfo(array $options = [])
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
    public function getStatistics($timestamp = '', array $options = [])
    {

    }

    /**
     * Retrieves the list of known data collections contained in a specified index.
     *
     * @param string $index Index containing the collections to be listed
     * @param array $options Optional parameters
     * @return array containing the list of stored and/or realtime collections on the provided index
     */
    public function listCollections($index = '', array $options = [])
    {

    }

    /**
     * Retrieves the list of indexes stored in Kuzzle.
     *
     * @param array $options Optional parameters
     * @return array of index names
     */
    public function listIndexes(array $options = [])
    {

    }

    /**
     * Log a user according to a strategy and credentials
     *
     * @param string $strategy Authentication strategy (local, facebook, github, …)
     * @param array $credentials Optional login credentials, depending on the strategy
     * @param string $expiresIn Login expiration time
     */
    public function login($strategy, array $credentials = [], $expiresIn = '')
    {

    }

    /**
     * Logs the user out.
     */
    public function logout()
    {

    }

    /**
     * A static Kuzzle\MemoryStorage instance
     * 
     * @return MemoryStorage
     */
    public function memoryStorage()
    {
        static $memoryStorage;

        if (is_null($memoryStorage))
        {
            $memoryStorage = new MemoryStorage($this);
        }

        return $memoryStorage;
    }

    /**
     * Retrieves the current Kuzzle time.
     *
     * @param array $options Optional parameters
     */
    public function now(array $options = [])
    {

    }

    /**
     * Base method used to send queries to Kuzzle
     *
     * @param array $queryArgs Query base arguments
     * @param array $query Query to execute
     * @param array $options Optional parameters
     * @return array
     *
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function query(array $queryArgs, array $query = [], array $options = [])
    {
        $request = [
            'action' => $queryArgs['action'],
            'controller' => $queryArgs['controller'],
            'metadata' => $this->metadata
        ];

        if (!empty($options))
        {
            if (array_key_exists('metadata', $options))
            {
                foreach ($options['metadata'] as $meta)
                {
                    $request['metadata'][$meta] = $options['metadata'][$meta];
                }
            }
        }

        if (array_key_exists('metadata', $query))
        {
            foreach ($query['metadata'] as $meta)
            {
                $request['metadata'][$meta] = $options['metadata'][$meta];
            }
        }

        foreach ($query as $attr => $value)
        {
            if ($attr !== 'metadata')
            {
                $request[$attr] = $value;
            }
        }

        foreach ($this->headers as $header)
        {
            if (!array_key_exists($header, $request['headers']))
            {
                $request['headers'][$header] = $this->headers[$header];
            }
        }

        /*
        * Do not add the token for the checkToken route, to avoid getting a token error when
        * a developer simply wish to verify his token
        */
        if ($this->jwtToken && !($request['controller'] === 'auth' && $request['action'] === 'checkToken'))
        {
            if (!is_array($request['headers']))
            {
                $request['headers'] = [];
            }

            $request['headers']['authorization'] = 'Bearer ' . $this->jwtToken;
        }

        if (array_key_exists('collection', $queryArgs))
        {
            $request['collection'] = $queryArgs['collection'];
        }

        if (array_key_exists('index', $queryArgs))
        {
            $request['index'] = $queryArgs['index'];
        }

        if (!array_key_exists('requestId', $request))
        {
            $request['requestId'] = Uuid::uuid4()->toString();
        }

        return $this->emitRestRequest($this->convertRestRequest($request));
    }

    /**
     * Given an index, the refresh action forces a refresh, on it,
     * making the documents visible to search immediately.
     *
     * @param string $index Optional. The index to refresh. If not set, defaults to Kuzzle->defaultIndex.
     * @param array $options Optional parameters
     * @return array structure matching the response from Elasticsearch
     */
    public function refreshIndex($index = '', array $options = [])
    {

    }

    /**
     * @param string $event One of the event described in the Event Handling section of the kuzzle documentation
     */
    public function removeAllListeners($event = '')
    {

    }

    /**
     * @param string $event One of the event described in the Event Handling section of the kuzzle documentation
     * @param string $listenerID The ID returned by Kuzzle->addListener()
     */
    public function removeListener($event, $listenerID)
    {

    }

    /**
     * A static Kuzzle\Security instance
     *
     * @return Security
     */
    public function security()
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
    public function setAutoRefresh($index = '', $autoRefresh = false, $options = [])
    {

    }

    /**
     * Set the default data index. Has the same effect than the defaultIndex constructor option.
     *
     * @param $index
     */
    public function setDefaultIndex($index)
    {

    }

    /**
     * Performs a partial update on the current user.
     *
     * @param array $content
     * @param array $options
     */
    public function updateSelf(array $content, $options = [])
    {

    }

    /**
     * This is a helper function returning itself, allowing to easily chain calls.
     *
     * @param array $headers
     * @param bool $replace
     * @return Kuzzle
     */
    public function setHeaders(array $headers, $replace = false)
    {

        return $this;
    }

    /**
     * Sets the internal JWT token which will be used to request kuzzle.
     *
     * @param string $jwtToken Previously generated JSON Web Token
     * @return Kuzzle
     */
    public function setJwtToken($jwtToken)
    {

        return $this;
    }

    /**
     * Retrieves current user object.
     *
     * @return User
     */
    public function whoAmI()
    {

    }

    /**
     * @param array $httpRequest
     * @return array
     *
     * @throws HttpException
     * @throws ErrorException
     */
    private function emitRestRequest(array $httpRequest)
    {
        $curlResource = curl_init();
        curl_setopt($curlResource, CURLOPT_URL, $this->url . $httpRequest['route']);
        curl_setopt($curlResource, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($curlResource, CURLOPT_CUSTOMREQUEST, $httpRequest['method']);
        curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlResource, CURLOPT_ENCODING, '');
        curl_setopt($curlResource, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curlResource, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlResource, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        if (array_key_exists('body', $httpRequest['request']))
        {
            curl_setopt($curlResource, CURLOPT_POSTFIELDS, json_encode($httpRequest['request']['body']));
        }

        $response = curl_exec($curlResource);
        $error = curl_error($curlResource);

        curl_close($curlResource);

        if (!empty($error))
        {
            throw new HttpException($error);
        }

        $response = json_decode($response, true);

        if (!empty($response['error']))
        {
            throw new ErrorException($response['error']['message']);
        }

        return $response;
    }

    /**
     * @throws Exception
     */
    private function getRouteDescription()
    {
        $routeConfigFile = realpath(__DIR__ . '/./config/routes.json');

        if (!file_exists($routeConfigFile))
        {
            throw new \Exception('Unable to find http routes configuration file (' . __DIR__ . '/./config/routes.json)');
        }

        $this->routesDescription = json_decode(file_get_contents($routeConfigFile), true);
    }

    /**
     * @param array $request
     * @return array
     *
     * @throws InvalidArgumentException
     */
    private function convertRestRequest(array $request)
    {
        $httpRequest = [
            'request' => $request
        ];

        if (!array_key_exists('route', $request))
        {
            if (!array_key_exists($request['controller'], $this->routesDescription))
            {
                throw new InvalidArgumentException('Unable to retrieve http route: controller "' . $request['controller'] . '" information not found');
            }

            if (!array_key_exists($request['action'], $this->routesDescription[$request['controller']]))
            {
                throw new InvalidArgumentException('Unable to retrieve http route: action "' . $request['controller'] . ':' . $request['action'] . '" information not found');
            }

            $httpRequest['route'] = $this->routesDescription[$request['controller']][$request['action']]['route'];
        }
        else
        {
            $httpRequest['route'] = $request['route'];
        }

        // replace http route parameters
        $httpRequest['route'] = str_replace(':collection', $request['collection'], $httpRequest['route']);
        $httpRequest['route'] = str_replace(':index', $request['index'], $httpRequest['route']);

        if (!array_key_exists('method', $httpRequest))
        {
            if (!array_key_exists($request['controller'], $this->routesDescription))
            {
                throw new InvalidArgumentException('Unable to retrieve http method: controller "' . $request['controller'] . '" information not found');
            }

            if (!array_key_exists($request['action'], $this->routesDescription[$request['controller']]))
            {
                throw new InvalidArgumentException('Unable to retrieve http method: action "' . $request['controller'] . ':' . $request['action'] . '" information not found');
            }

            $httpRequest['method'] = mb_strtoupper($this->routesDescription[$request['controller']][$request['action']]['method']);
        }
        else
        {
            $httpRequest['method'] = mb_strtoupper($request['method']);
        }

        return $httpRequest;
    }
}