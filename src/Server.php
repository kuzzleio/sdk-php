<?php

namespace Kuzzle;

use DateTime;

/**
 * Class Server
 * @package kuzzleio/kuzzle-sdk
 */
class Server
{
    /**
    * @var Kuzzle linked Kuzzle instance
    */
    protected $kuzzle;

    /**
     * Server controller constructor.
     *
     * @param Kuzzle $kuzzle Kuzzle server instance
     * @return Server
     */
    public function __construct(Kuzzle $kuzzle)
    {
        $this->kuzzle = $kuzzle;
        return $this;
    }

    /**
     * Retrieves the current Kuzzle time.
     *
     * @param array $options Optional parameters
     * @return DateTime
     */
    public function now(array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('server', 'now'),
            [],
            $options
        );

        return new DateTime('@' . round($response['result']['now'] / 1000));
    }

    /**
     * Retrieves information about Kuzzle, its plugins and active services.
     *
     * @param array $options Optional parameters
     * @return array containing server information
     */
    public function getInfo(array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('server', 'info'),
            [],
            $options
        );

        return $response['result']['serverInfo'];
    }

    /**
     * Returns the current Kuzzle configuration.
     *
     * @param string $timestamp starting time from which the frames are to be retrieved
     * @param array $options Optional parameters
     *
     * @return array containing one or more statistics frame(s)
     */
    public function getConfig(array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('server', 'getConfig'),
            [],
            $options
        );

        return [$response['result']];
    }

    /**
     * Kuzzle monitors active connections, and ongoing/completed/failed requests.
     * This method allows getting a set of frames starting from a provided timestamp.
     *
     * @param string $timestamp Optional starting time from which the frames are to be retrieved
     * @param array $options Optional parameters
     *
     * @return array containing one or more statistics frame(s)
     */
    public function getStats($timestamp = '', array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('server', 'getStats'),
            [
              'body' => [
                  'startTime' => $timestamp
              ]
            ],
            $options
        );

        return $response['result']['hits'];
    }

    /**
     * Kuzzle monitors active connections, and ongoing/completed/failed requests.
     * This method allows getting either the last statistics frame.
     *
     *
     * @param array $options Optional parameters
     * @return array[] containing one or more statistics frame(s)
     */
    public function getLastStats(array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('server', 'getLastStats'),
            [],
            $options
        );

        return [$response['result']];
    }

    /**
     * Kuzzle monitors active connections, and ongoing/completed/failed requests.
     * This method returns all available statistics from Kuzzle.
     *
     * @param array $options Optional parameters
     * @return array each one of them being a statistic frame
     */
    public function getAllStats(array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('server', 'getAllStats'),
            [],
            $options
        );

        return $response['result']['hits'];
    }

    /**
     * Checks if an administrator account has been created.
     *
     * @param array $options Optional parameters
     * @return boolean true if it exists and false if it does not.
     */
    public function adminExists(array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('server', 'adminExists'),
            [],
            $options
        );

        return $response['result']['exists'];
    }
}
