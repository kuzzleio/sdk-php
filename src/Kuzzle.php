<?php

namespace Kuzzle;

use DateTime;
use ErrorException;
use Exception;
use InvalidArgumentException;

use Kuzzle\Util\RequestInterface;
use Ramsey\Uuid\Uuid;

use Kuzzle\Util\CurlRequest;
use Kuzzle\Security\Security;
use Kuzzle\Security\User;

/**
 * Class Kuzzle
 * @package kuzzleio/kuzzle-sdk
 */
class Kuzzle
{
    const ROUTE_DESCRIPTION_FILE = './config/routes.json';
    const DEFAULT_REQUEST_HANDLER = 'Kuzzle\Util\CurlRequest';

    /**
     * @var string url of kuzzle http server
     */
    protected $url;

    /**
     * @var string port of kuzzle http server (default: 7512)
     */
    protected $port = 7512;

    /**
     * @var string Kuzzle’s default index to use
     */
    protected $defaultIndex;

    /**
     * @var array Common headers for all sent documents.
     */
    protected $headers = [];

    /**
     * @var array Common volatile data, will be sent to all future requests
     */
    protected $volatile = [];

    /**
     * @var string Token used in requests for authentication.
     */
    protected $jwtToken;

    /**
     * @var Collection[][]
     */
    protected $collections = [];

    /**
     * @var array[]
     */
    protected $listeners = [];

    /**
     * @var array
     */
    protected $routesDescription = [];

    /**
     * @var string
     */
    protected $routesDescriptionFile;

    /**
     * @var RequestInterface
     */
    protected $requestHandler;

    /**
     * @var string
     */
    protected $sdkVersion;


    /**
     * Kuzzle constructor.
     *
     * @param string $host Server name/IP address to the target Kuzzle instance
     * @param array $options Optional Kuzzle connection configuration
     * @return Kuzzle
     */
    public function __construct($host, array $options = [])
    {
        $this->routesDescriptionFile = self::ROUTE_DESCRIPTION_FILE;

        if (array_key_exists('routesDescriptionFile', $options)) {
            $this->routesDescriptionFile = $options['routesDescriptionFile'];
        }

        if (array_key_exists('defaultIndex', $options)) {
            $this->defaultIndex = $options['defaultIndex'];
        }

        if (array_key_exists('requestHandler', $options)) {
            $this->setRequestHandler($options['requestHandler']);
        } else {
            $this->requestHandler = new CurlRequest();
        }

        if (array_key_exists('port', $options)) {
            $this->port = $options['port'];
        }

        $this->url = 'http://' . $host . ':' . $this->port;
        $this->loadRoutesDescription($this->routesDescriptionFile);

        $this->sdkVersion = json_decode(file_get_contents(__DIR__.'/../composer.json'))->version;

        return $this;
    }

    /**
     * Adds a listener to a Kuzzle global event.
     * When an event is fired, listeners are called in the order of their insertion.
     *
     * @param string $event One of the event described in the Event Handling section of the kuzzle documentation
     * @param callable $listener The function to call each time one of the registered event is fired
     *
     * @return Kuzzle
     * @throws InvalidArgumentException
     */
    public function addListener($event, $listener)
    {
        if (!is_callable($listener)) {
            throw new InvalidArgumentException('Unable to add a listener on event "' . $event . '": given listener is not callable');
        }

        if (!array_key_exists($event, $this->listeners)) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][spl_object_hash($listener)] = $listener;

        return $this;
    }

    /**
     * Emit an event to all registered listeners
     *
     * @param string $event One of the event described in the Event Handling section of the kuzzle documentation
     *
     * @return Kuzzle
     */
    public function emitEvent($event)
    {
        if (array_key_exists($event, $this->listeners)) {
            $arg_list = func_get_args();
            array_shift($arg_list);
            foreach ($this->listeners[$event] as $callback) {
                call_user_func_array($callback, $arg_list);
            }
        }

        return $this;
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
     * Create an index
     *
     * @param $index
     * @param array $options
     * @return mixed
     */
    public function createIndex($index, array $options = [])
    {
        $options['httpParams'] = [
            ':index' => $index
        ];

        $response = $this->query(
            $this->buildQueryArgs('index', 'create'),
            [
                'body' => ['index' => $index]
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
     * @return Collection
     *
     * @throws InvalidArgumentException
     */
    public function collection($collection, $index = '')
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
            $this->collections[$index][$collection] = new Collection($this, $collection, $index);
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
            $this->buildQueryArgs('server', 'getAllStats'),
            [],
            $options
        );

        return $response['result']['hits'];
    }

    /**
     * @return string
     */
    public function getDefaultIndex()
    {
        return $this->defaultIndex;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
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
     * @return array
     */
    public function getVolatile()
    {
        return $this->volatile;
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
            $this->buildQueryArgs('server', 'info'),
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
            $this->buildQueryArgs('server', $action),
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

        $query = [
            'body' => [
                'type' => $collectionType
            ]
        ];

        $response = $this->query($this->buildQueryArgs('collection', 'list', $index), $query, $options);

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
            $this->buildQueryArgs('index', 'list'),
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

        if (empty($strategy)) {
            throw new InvalidArgumentException('Unable to login: no strategy specified');
        }

        if (!array_key_exists('httpParams', $options)) {
            $options['httpParams'] = [];
        }

        if (!array_key_exists(':strategy', $options['httpParams'])) {
            $options['httpParams'][':strategy'] = $strategy;
        }

        if (!empty($expiresIn)) {
            $options['query_parameters']['expiresIn'] = $expiresIn;
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
            $this->buildQueryArgs('server', 'now'),
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
        $httpParams = [
            'query_parameters' => []
        ];
        $request = [
            'volatile' => $this->volatile
        ];

        if (array_key_exists('controller', $queryArgs)) {
            $request['controller'] = $queryArgs['controller'];
        } else {
            if (!array_key_exists('route', $queryArgs) || !array_key_exists('method', $queryArgs)) {
                throw new InvalidArgumentException('Unable to execute query: missing controller or route/method');
            }

            $request['controller'] = '';
        }

        if (array_key_exists('action', $queryArgs)) {
            $request['action'] = $queryArgs['action'];
        } else {
            if (!array_key_exists('route', $queryArgs) || !array_key_exists('method', $queryArgs)) {
                throw new InvalidArgumentException('Unable to execute query: missing action or route/method');
            }

            $request['action'] = '';
        }

        if (!empty($options)) {
            if (array_key_exists('volatile', $options)) {
                foreach ($options['volatile'] as $meta => $value) {
                    $request['volatile'][$meta] = $value;
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

            if (isset($options['query_parameters'])) {
                $httpParams['query_parameters'] = array_merge($httpParams['query_parameters'], $options['query_parameters']);
            }

            foreach (['refresh', 'from', 'size', 'scroll'] as $optionParam) {
                if (array_key_exists($optionParam, $options)) {
                    $httpParams['query_parameters'][$optionParam] = $options[$optionParam];
                }
            }
        }

        if (array_key_exists('volatile', $query)) {
            foreach ($query['volatile'] as $meta => $value) {
                $request['volatile'][$meta] = $value;
            }
        }

        foreach ($query as $attr => $value) {
            if ($attr === 'body' && empty($value)) {
                $request['body'] = (object)[];
            } else if ($attr !== 'volatile') {
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

        foreach (['collection', 'route', 'method', 'index'] as $queryArg) {
            if (array_key_exists($queryArg, $queryArgs)) {
                $request[$queryArg] = $queryArgs[$queryArg];
            }
        }

        if (!array_key_exists('requestId', $request)) {
            $request['requestId'] = Uuid::uuid4()->toString();
        }
         // @todo move this into RequestHandler
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
            $this->buildQueryArgs('index', 'refresh', $index),
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
     * @param callback $listener The listener callback
     */
    public function removeListener($event, $listener)
    {
        if (array_key_exists($event, $this->listeners)) {
            $key = array_search($listener, $this->listeners[$event]);
            if ($key !== false) {
                unset($this->listeners[$event][$key]);
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
            $this->buildQueryArgs('index', 'setAutoRefresh', $index),
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
     * Returns de current autoRefresh status for the given index
     *
     * @param string $index Optional TThe index to get the status from. Defaults to Kuzzle->defaultIndex
     * @param array $options Optional parameters
     * @return boolean
     */
    public function getAutoRefresh($index = '', array $options = [])
    {
        if (empty($index)) {
            if (empty($this->defaultIndex)) {
                throw new InvalidArgumentException('Unable to set auto refresh on index: no index specified');
            }

            $index = $this->defaultIndex;
        }

        $response = $this->query(
            $this->buildQueryArgs('index', 'getAutoRefresh', $index),
            [],
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
     * @param array $volatile
     * @param bool $replace
     * @return Kuzzle
     */
    public function setVolatile(array $volatile, $replace = false)
    {
        if ($replace) {
            $this->volatile = $volatile;
        } else {
            foreach ($volatile as $key => $value) {
                $this->volatile[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSdkVersion()
    {
        return $this->sdkVersion;
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

        return new User($this->security(), $response['result']['_id'], $response['result']['_source'], $response['result']['_meta']);
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
     * @param RequestInterface $handler
     */
    public function setRequestHandler(RequestInterface $handler)
    {
        $this->requestHandler = $handler;
    }

    /**
     * Create credentials of the specified <strategy> for the current user.
     *
     * @param $strategy
     * @param $credentials
     * @param array $options
     * @return mixed
     */
    public function createMyCredentials($strategy, $credentials, array $options = [])
    {
        $options['httpParams'][':strategy'] = $strategy;

        return $this->query($this->buildQueryArgs('auth', 'createMyCredentials'), ['body' => $credentials], $options)['result'];
    }

    /**
     * Delete credentials of the specified <strategy> for the current user.
     *
     * @param $strategy
     * @param array $options
     * @return mixed
     */
    public function deleteMyCredentials($strategy, array $options = [])
    {
        $options['httpParams'][':strategy'] = $strategy;

        return $this->query($this->buildQueryArgs('auth', 'deleteMyCredentials'), [], $options)['result'];
    }

    /**
     * Get credential information of the specified <strategy> for the current user.
     *
     * @param $strategy
     * @param array $options
     * @return mixed
     */
    public function getMyCredentials($strategy, array $options = [])
    {
        $options['httpParams'][':strategy'] = $strategy;

        return $this->query($this->buildQueryArgs('auth', 'getMyCredentials'), [], $options)['result'];
    }

    /**
     * Update credentials of the specified <strategy> for the current user.
     *
     * @param $strategy
     * @param $credentials
     * @param array $options
     * @return mixed
     */
    public function updateMyCredentials($strategy, $credentials, array $options = [])
    {
        $options['httpParams'][':strategy'] = $strategy;

        return $this->query($this->buildQueryArgs('auth', 'updateMyCredentials'), ['body' => $credentials], $options)['result'];
    }

    /**
     * Validate credentials of the specified <strategy> for the current user.
     *
     * @param $strategy
     * @param $credentials
     * @param array $options
     * @return mixed
     */
    public function validateMyCredentials($strategy, $credentials, array $options = [])
    {
        $options['httpParams'][':strategy'] = $strategy;

        return $this->query($this->buildQueryArgs('auth', 'validateMyCredentials'), ['body' => $credentials], $options)['result'];
    }

    /**
     * @internal
     * @todo move this into RequestHandler
     * @param array $httpRequest
     * @return array
     *
     * @throws ErrorException
     */
    protected function emitRestRequest(array $httpRequest)
    {
        $body = '';
        $headers = [
            'Content-type: application/json'
        ];

        if (array_key_exists('headers', $httpRequest['request'])) {
            foreach ($httpRequest['request']['headers'] as $header => $value) {
                $headers[] = ucfirst($header) . ': ' . $value;
            }
        }
        $headers[] = 'X-Kuzzle-Volatile: ' . json_encode(array_merge($this->getVolatile(), ['sdkVersion' => $this->getSdkVersion()]));

        if (array_key_exists('body', $httpRequest['request'])) {
            $body = json_encode($httpRequest['request']['body']);
            $headers[] = 'Content-length: ' . strlen($body);
        }

        $result = $this->requestHandler->execute([
            'url' => $this->url . $httpRequest['route'],
            'method' => $httpRequest['method'],
            'headers' => $headers,
            'body' => $body,
            'query_parameters' => $httpRequest['query_parameters']
        ]);

        if (!empty($result['error'])) {
            $this->emitEvent('queryError', $result['error'], $httpRequest);
            throw new ErrorException($result['error']);
        }

        $response = json_decode($result['response'], true);

        /**
         * @todo: manage custom exceptions
         */
        if (!empty($response['error'])) {
            $this->emitEvent('queryError', $response['error'], $httpRequest);
            throw new ErrorException($response['error']['message']);
        }

        return $response;
    }

    /**
     * @internal
     * @todo move this into RequestHandler
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

        if (!is_array($routesDescription)
            || !array_key_exists('server', $routesDescription)
            || !is_array($routesDescription['server'])
            || !array_key_exists('now', $routesDescription['server'])
        ) {
            throw new Exception('Unable to parse http routes configuration file (' . $routeDescriptionFile . '): should return an array');
        }

        $this->routesDescription = $routesDescription;
    }

    /**
     * @internal
     * @todo move this into RequestHandler
     * @param array $request
     * @param array $httpParams
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function convertRestRequest(array $request, array $httpParams = [])
    {
        $httpRequest = [];

        if (array_key_exists('query_parameters', $httpParams)) {
            $httpRequest['query_parameters'] = $httpParams['query_parameters'];
            unset($httpParams['query_parameters']);
        }

        if (!array_key_exists('route', $request)) {
            if (!array_key_exists($request['controller'], $this->routesDescription)) {
                throw new InvalidArgumentException('Unable to retrieve http route: controller "' . $request['controller'] . '" information not found');
            }

            if (!array_key_exists($request['action'], $this->routesDescription[$request['controller']])) {
                throw new InvalidArgumentException('Unable to retrieve http route: action "' . $request['controller'] . ':' . $request['action'] . '" information not found');
            }

            $httpRequest['route'] = $this->routesDescription[$request['controller']][$request['action']]['route'];

            if ($request['controller'] == 'document' && $request['action'] == 'create') {
              $httpRequest['route'] = str_replace(':_id/', '', $httpRequest['route']);
            }
        } else {
            $httpRequest['route'] = $request['route'];
            unset($request['route']);
        }

        // replace http route parameters
        if (array_key_exists('collection', $request)) {
            $httpParams[':collection'] = $request['collection'];
        }

        if (array_key_exists('index', $request)) {
            $httpParams[':index'] = $request['index'];
        }

        if (array_key_exists('_id', $request)) {
            $httpParams[':_id'] = $request['_id'];
        }

        foreach ($httpParams as $pattern => $value) {
            $httpRequest['route'] = str_replace($pattern, $value, $httpRequest['route']);
        }

        if (!array_key_exists('method', $request)) {
            if (!array_key_exists($request['controller'], $this->routesDescription)) {
                throw new InvalidArgumentException('Unable to retrieve http method: controller "' . $request['controller'] . '" information not found');
            }

            if (!array_key_exists($request['action'], $this->routesDescription[$request['controller']])) {
                throw new InvalidArgumentException('Unable to retrieve http method: action "' . $request['controller'] . ':' . $request['action'] . '" information not found');
            }

            $httpRequest['method'] = mb_strtoupper($this->routesDescription[$request['controller']][$request['action']]['method']);
        } else {
            $httpRequest['method'] = mb_strtoupper($request['method']);
            unset($request['method']);
        }

        $httpRequest['request'] = $request;

        return $httpRequest;
    }
}
