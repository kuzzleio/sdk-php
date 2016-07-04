<?php

namespace Kuzzle;

use DateTime;
use ErrorException;
use Exception;
use InvalidArgumentException;

use Ramsey\Uuid\Uuid;

use Kuzzle\DataCollection;
use Kuzzle\Security\Security;
use Kuzzle\Security\User;

/**
 * Class Kuzzle
 * @package kuzzleio/kuzzle-sdk
 */
class Kuzzle
{
    const ROUTE_DESCRIPTION_FILE = './config/routes.json';

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
     * @var DataCollection[][]
     */
    protected $collections = [];

    /**
     * @var array[]
     */
    protected $listeners = [];

    /**
     * @var array
     */
    private $routesDescription = [];

    /**
     * @var string
     */
    private $routesDescriptionFile;


    /**
     * Kuzzle constructor.
     *
     * @param string $url URL to the target Kuzzle instance
     * @param array $options Optional Kuzzle connection configuration
     * @return Kuzzle
     */
    public function __construct($url, array $options = [])
    {
        $this->url = $url;
        $this->routesDescriptionFile = self::ROUTE_DESCRIPTION_FILE;
        
        if (array_key_exists('routesDescriptionFile', $options)) {
            $this->routesDescriptionFile = $options['routesDescriptionFile'];
        }

        if (array_key_exists('defaultIndex', $options)) {
            $this->defaultIndex = $options['defaultIndex'];
        }

        $this->loadRoutesDescription($this->routesDescriptionFile);

        return $this;
    }

    /**
     * Adds a listener to a Kuzzle global event.
     * When an event is fired, listeners are called in the order of their insertion.
     *
     * @param string $event One of the event described in the Event Handling section of the kuzzle documentation
     * @param callable $listener The function to call each time one of the registered event is fired
     * @return string containing an unique listener ID.
     *
     * @throws InvalidArgumentException
     */
    public function addListener($event, $listener)
    {
        if (!is_callable($listener)) {
            throw new InvalidArgumentException('Unable to add a listener on event "' . $event . '": given listener is not callable');
        }

        $listenerId = uniqid();

        if (array_key_exists($event, $this->listeners)) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][$listenerId] = $listener;

        return $listenerId;
    }

    /**
     * Checks the validity of a JSON Web Token.
     *
     * @param string $token The token to check
     * @param array $options Optional parameters
     * @return array with a valid boolean property.
     *         If the token is valid, a expiresAt property is set with the expiration timestamp.
     *         If not, a state property is set explaining why the token is invalid.
     */
    public function checkToken($token, array $options = [])
    {
        $response = $this->query(
            $this->buildQueryArgs('auth', 'checkToken'),
            [
                'body' => ['token' => $token]
            ],
            $options
        );

        return $response['result'];
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
    public function dataCollectionFactory($collection, $index = '')
    {
        if (empty($index)) {
            if (empty($this->defaultIndex)) {
                throw new InvalidArgumentException('Unable to create a new data collection object: no index specified');
            }

            $index = $this->defaultIndex;
        }

        if (!array_key_exists($index, $this->collections)) {
            $this->collections[$index] = [];
        }

        if (!array_key_exists($collection, $this->collections[$index])) {
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
        $response = $this->query(
            $this->buildQueryArgs('admin', 'getAllStats'),
            [],
            $options
        );

        return $response['result']['hits'];
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
        $response = $this->query(
            $this->buildQueryArgs('auth', 'getMyRights'),
            [],
            $options
        );

        return $response['result']['hits'];
    }

    /**
     * Retrieves information about Kuzzle, its plugins and active services.
     *
     * @param array $options Optional parameters
     * @return array containing server information
     */
    public function getServerInfo(array $options = [])
    {
        $response = $this->query(
            $this->buildQueryArgs('read', 'serverInfo'),
            [],
            $options
        );

        return $response['result']['serverInfo'];
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
        $data = [];

        if (empty($timestamp)) {
            $action = 'getLastStats';
        } else {
            $action = 'getStats';
            $data['body'] = [
                'startTime' => $timestamp
            ];
        }

        $response = $this->query(
            $this->buildQueryArgs('admin', $action),
            $data,
            $options
        );

        return empty($timestamp) ? [$response['result']] : $response['result']['hits'];
    }

    /**
     * Retrieves the list of known data collections contained in a specified index.
     *
     * @param string $index Index containing the collections to be listed
     * @param array $options Optional parameters
     * @return array containing the list of stored and/or realtime collections on the provided index
     *
     * @throws InvalidArgumentException
     */
    public function listCollections($index = '', array $options = [])
    {
        $collectionType = 'all';

        if (empty($index)) {
            if (empty($this->defaultIndex)) {
                throw new InvalidArgumentException('Unable to list collections: no index specified');
            }

            $index = $this->defaultIndex;
        }

        if (array_key_exists('type', $options)) {
            $collectionType = $options['type'];
        }

        $options['httpParams'] = [
            ':type' => $collectionType
        ];

        $response = $this->query(
            $this->buildQueryArgs('read', 'listCollections', $index),
            [
                'body' => [
                    'type' => $collectionType
                ]
            ],
            $options
        );

        return $response['result']['collections'];
    }

    /**
     * Retrieves the list of indexes stored in Kuzzle.
     *
     * @param array $options Optional parameters
     * @return array of index names
     */
    public function listIndexes(array $options = [])
    {
        $response = $this->query(
            $this->buildQueryArgs('read', 'listIndexes'),
            [],
            $options
        );

        return $response['result']['indexes'];
    }

    /**
     * Log a user according to a strategy and credentials
     *
     * @param string $strategy Authentication strategy (local, facebook, github, …)
     * @param array $credentials Optional login credentials, depending on the strategy
     * @param string $expiresIn Login expiration time
     * @param array $options Optional options
     * @return array
     */
    public function login($strategy, array $credentials = [], $expiresIn = '', array $options = [])
    {
        $body = $credentials;

        if (!empty($expiresIn)) {
            $body['expiresIn'] = $expiresIn;
        }

        if (!array_key_exists('httpParams', $options)) {
            $options['httpParams'] = [];
        }

        if (!array_key_exists(':strategy', $options['httpParams'])) {
            $options['httpParams'][':strategy'] = $strategy;
        }

        $response = $this->query(
            $this->buildQueryArgs('auth', 'login'),
            [
                'body' => $body
            ],
            $options
        );

        if ($response['result']['jwt']) {
            $this->jwtToken = $response['result']['jwt'];
        }

        return $response['result'];
    }

    /**
     * Logs the user out.
     * @param array $options Optional parameters
     * @return array
     */
    public function logout(array $options = [])
    {
        $response = $this->query(
            $this->buildQueryArgs('auth', 'logout'),
            [],
            $options
        );

        $this->jwtToken = null;

        return $response['result'];
    }

    /**
     * A static Kuzzle\MemoryStorage instance
     *
     * @return MemoryStorage
     */
    public function memoryStorage()
    {
        static $memoryStorage;

        if (is_null($memoryStorage)) {
            $memoryStorage = new MemoryStorage($this);
        }

        return $memoryStorage;
    }

    /**
     * Retrieves the current Kuzzle time.
     *
     * @param array $options Optional parameters
     * @return DateTime
     */
    public function now(array $options = [])
    {
        $response = $this->query(
            $this->buildQueryArgs('read', 'now'),
            [],
            $options
        );

        return new DateTime('@' . round($response['result']['now'] / 1000));
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
        $httpParams = [];
        $request = [
            'action' => $queryArgs['action'],
            'controller' => $queryArgs['controller'],
            'metadata' => $this->metadata
        ];

        if (!empty($options)) {
            if (array_key_exists('metadata', $options)) {
                foreach ($options['metadata'] as $meta => $value) {
                    $request['metadata'][$meta] = $value;
                }
            }
            if (array_key_exists('requestId', $options)) {
                $request['requestId'] = $options['requestId'];
            }
            if (array_key_exists('httpParams', $options)) {
                foreach ($options['httpParams'] as $param => $value) {
                    $httpParams[$param] = $value;
                }
            }
        }

        if (array_key_exists('metadata', $query)) {
            foreach ($query['metadata'] as $meta => $value) {
                $request['metadata'][$meta] = $value;
            }
        }

        foreach ($query as $attr => $value) {
            if ($attr !== 'metadata') {
                $request[$attr] = $value;
            }
        }

        $request = $this->addHeaders($request, $this->headers);

        /*
        * Do not add the token for the checkToken route, to avoid getting a token error when
        * a developer simply wish to verify his token
        */
        if ($this->jwtToken && !($request['controller'] === 'auth' && $request['action'] === 'checkToken')) {
            if (array_key_exists('headers', $request) && !is_array($request['headers'])) {
                $request['headers'] = [];
            }

            $request['headers']['authorization'] = 'Bearer ' . $this->jwtToken;
        }

        if (array_key_exists('collection', $queryArgs)) {
            $request['collection'] = $queryArgs['collection'];
        }

        if (array_key_exists('index', $queryArgs)) {
            $request['index'] = $queryArgs['index'];
        }

        if (!array_key_exists('requestId', $request)) {
            $request['requestId'] = Uuid::uuid4()->toString();
        }

        return $this->emitRestRequest($this->convertRestRequest($request, $httpParams));
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
        if (empty($index)) {
            if (empty($this->defaultIndex)) {
                throw new InvalidArgumentException('Unable to refresh index: no index specified');
            }

            $index = $this->defaultIndex;
        }

        $response = $this->query(
            $this->buildQueryArgs('admin', 'refreshIndex', $index),
            [],
            $options
        );

        return $response['result'];
    }

    /**
     * @param string $event One of the event described in the Event Handling section of the kuzzle documentation
     */
    public function removeAllListeners($event = '')
    {
        if (empty($event)) {
            $this->listeners = [];
        } elseif (array_key_exists($event, $this->listeners)) {
            unset($this->listeners[$event]);
        }
    }

    /**
     * @param string $event One of the event described in the Event Handling section of the kuzzle documentation
     * @param string $listenerID The ID returned by Kuzzle->addListener()
     */
    public function removeListener($event, $listenerID)
    {
        if (array_key_exists($event, $this->listeners)) {
            if (array_key_exists($listenerID, $this->listeners[$event])) {
                unset($this->listeners[$event][$listenerID]);
            }
        }
    }

    /**
     * A static Kuzzle\Security instance
     *
     * @return Security
     */
    public function security()
    {
        static $security;

        if (is_null($security)) {
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
     * @return boolean
     */
    public function setAutoRefresh($index = '', $autoRefresh = false, array $options = [])
    {
        if (empty($index)) {
            if (empty($this->defaultIndex)) {
                throw new InvalidArgumentException('Unable to set auto refresh on index: no index specified');
            }

            $index = $this->defaultIndex;
        }

        $response = $this->query(
            $this->buildQueryArgs('admin', 'setAutoRefresh', $index),
            [
                'body' => [
                    'autoRefresh' => $autoRefresh
                ]
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Set the default data index. Has the same effect than the defaultIndex constructor option.
     *
     * @param $index
     * @return Kuzzle
     */
    public function setDefaultIndex($index)
    {
        $this->defaultIndex = $index;

        return $this;
    }

    /**
     * Performs a partial update on the current user.
     *
     * @param array $content a plain javascript object representing the user's modification
     * @param array $options (optional) arguments
     * @return array
     */
    public function updateSelf(array $content, array $options = [])
    {
        $response = $this->query(
            $this->buildQueryArgs('auth', 'updateSelf'),
            [
                'body' => $content
            ],
            $options
        );

        return $response['result'];
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
        if ($replace) {
            $this->headers = $headers;
        } else {
            foreach ($headers as $key => $value) {
                $this->headers[$key] = $value;
            }
        }

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
        $this->jwtToken = $jwtToken;

        return $this;
    }

    /**
     * Retrieves current user object.
     *
     * @param array $options (optional) arguments
     * @return User
     */
    public function whoAmI(array $options = [])
    {
        $response = $this->query(
            $this->buildQueryArgs('auth', 'getCurrentUser'),
            [],
            $options
        );

        return new User($this->security(), $response['result']['_id'], $response['result']['_source']);
    }
    
    /**
     * @param array $query
     * @param array $headers
     * @return array
     */
    public function addHeaders(array $query, array $headers)
    {
        foreach ($headers as $header => $value) {
            if (!array_key_exists($header, $query)) {
                $query[$header] = $value;
            }
        }

        return $query;
    }

    /**
     * @param $controller
     * @param $action
     * @param string $index
     * @param string $collection
     * @return array
     */
    public function buildQueryArgs($controller, $action, $index = '', $collection = '')
    {
        $queryArgs = [
            'controller' => $controller,
            'action' => $action
        ];

        if (!empty($index)) {
            $queryArgs['index'] = $index;
        }

        if (!empty($collection)) {
            $queryArgs['collection'] = $collection;
        }

        return $queryArgs;
    }

    /**
     * @param array $httpRequest
     * @return array
     *
     * @throws ErrorException
     */
    protected function emitRestRequest(array $httpRequest)
    {
        $headers = [
            'Content-type: application/json'
        ];

        if (array_key_exists('headers', $httpRequest['request'])) {
            foreach ($httpRequest['request']['headers'] as $header => $value) {
                $headers[] = ucfirst($header) . ': ' . $value;
            }
        }

        $curlResource = curl_init();
        curl_setopt($curlResource, CURLOPT_URL, $this->url . $httpRequest['route']);
        curl_setopt($curlResource, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlResource, CURLOPT_CUSTOMREQUEST, $httpRequest['method']);
        curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlResource, CURLOPT_ENCODING, '');
        curl_setopt($curlResource, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curlResource, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlResource, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        /**
         * @todo: handle http proxy via options
         */

        if (array_key_exists('body', $httpRequest['request'])) {
            $body = json_encode($httpRequest['request']['body'], JSON_FORCE_OBJECT);
            curl_setopt($curlResource, CURLOPT_POSTFIELDS, $body);
        }

        $response = curl_exec($curlResource);
        $error = curl_error($curlResource);

        curl_close($curlResource);

        if (!empty($error)) {
            throw new ErrorException($error);
        }

        $response = json_decode($response, true);

        if (!empty($response['error'])) {
            throw new ErrorException($response['error']['message']);
        }

        return $response;
    }

    /**
     * @param string $routeDescriptionFile
     * @throws Exception
     */
    protected function loadRoutesDescription($routeDescriptionFile)
    {
        $routeDescriptionFilePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . $routeDescriptionFile);

        if (!file_exists($routeDescriptionFilePath)) {
            throw new Exception('Unable to find http routes configuration file (' . $routeDescriptionFile . ')');
        }

        $routesDescription = json_decode(file_get_contents($routeDescriptionFilePath), true);

        if (!is_array($routesDescription)) {
            throw new Exception('Unable to parse http routes configuration file (' . $routeDescriptionFile . '): should return an array');
        }

        if (!array_key_exists('read', $routesDescription)) {
            throw new Exception('Unable to parse http routes configuration file (' . $routeDescriptionFile . '): should return an array');
        }

        if (!is_array($routesDescription['read']) || !array_key_exists('now', $routesDescription['read'])) {
            throw new Exception('Unable to parse http routes configuration file (' . $routeDescriptionFile . '): should return an array');
        }

        $this->routesDescription = $routesDescription;
    }

    /**
     * @param array $request
     * @param array $httpParams
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function convertRestRequest(array $request, array $httpParams = [])
    {
        $httpRequest = [
            'request' => $request
        ];

        if (!array_key_exists('route', $request)) {
            if (!array_key_exists($request['controller'], $this->routesDescription)) {
                throw new InvalidArgumentException('Unable to retrieve http route: controller "' . $request['controller'] . '" information not found');
            }

            if (!array_key_exists($request['action'], $this->routesDescription[$request['controller']])) {
                throw new InvalidArgumentException('Unable to retrieve http route: action "' . $request['controller'] . ':' . $request['action'] . '" information not found');
            }

            $httpRequest['route'] = $this->routesDescription[$request['controller']][$request['action']]['route'];
        } else {
            $httpRequest['route'] = $request['route'];
        }

        // replace http route parameters
        if (array_key_exists('collection', $request)) {
            $httpParams[':collection'] = $request['collection'];
        }

        if (array_key_exists('index', $request)) {
            $httpParams[':index'] = $request['index'];
        }

        if (array_key_exists('_id', $request)) {
            $httpParams[':id'] = $request['_id'];
        }

        foreach ($httpParams as $pattern => $value) {
            $httpRequest['route'] = str_replace($pattern, $value, $httpRequest['route']);
        }

        if (!array_key_exists('method', $httpRequest)) {
            if (!array_key_exists($request['controller'], $this->routesDescription)) {
                throw new InvalidArgumentException('Unable to retrieve http method: controller "' . $request['controller'] . '" information not found');
            }

            if (!array_key_exists($request['action'], $this->routesDescription[$request['controller']])) {
                throw new InvalidArgumentException('Unable to retrieve http method: action "' . $request['controller'] . ':' . $request['action'] . '" information not found');
            }

            $httpRequest['method'] = mb_strtoupper($this->routesDescription[$request['controller']][$request['action']]['method']);
        } else {
            $httpRequest['method'] = mb_strtoupper($request['method']);
        }

        return $httpRequest;
    }
}
