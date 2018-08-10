<?php

namespace Kuzzle;

use InvalidArgumentException;

/**
 * Class Auth
 * @package kuzzleio/kuzzle-sdk
 */
class Auth
{
    /**
    * @var Kuzzle linked Kuzzle instance
    */
    protected $kuzzle;

    /**
     * Auth controller constructor.
     *
     * @param Kuzzle $kuzzle Kuzzle server instance
     * @return Auth
     */
    public function __construct($kuzzle)
    {
        $this->kuzzle = $kuzzle;
        return $this;
    }

    /**
     * Checks the validity of a JSON Web Token.
     *
     * @param string $token The token to check
     * @param array $options Optional parameters
     *
     * @return array with a valid boolean property.
     *         If the token is valid, a expiresAt property is set with the expiration timestamp.
     *         If not, a state property is set explaining why the token is invalid.
     *
     * @throws InvalidArgumentException
     */
    public function checkToken($token, array $options = [])
    {
        if (empty($token)) {
            throw new InvalidArgumentException('Kuzzle\Auth::checkToken: cannot check empty token');
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('auth', 'checkToken'),
            [
                'body' => [
                    'token' => $token
                ]
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Log a user according to a strategy and credentials
     *
     * @param string $strategy Authentication strategy (local, facebook, github, …)
     * @param array $credentials Optional login credentials, depending on the strategy
     * @param string $expiresIn Login expiration time
     * @param array $options Optional options
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function login($strategy, array $credentials = [], $expiresIn = '', array $options = [])
    {
        if (empty($strategy)) {
            throw new InvalidArgumentException('Kuzzle\Auth::login: Unable to login: no strategy specified');
        }

        if (empty($credentials)) {
            throw new InvalidArgumentException('Kuzzle\Auth::login: Unable to login: no strategy specified');
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

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('auth', 'login'),
            [
                'body' => json_encode($credentials)
            ],
            $options
        );

        if ($response['result']['jwt']) {
            $this->kuzzle->setJwtToken($response['result']['jwt']);
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
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('auth', 'logout'),
            [],
            $options
        );

        $this->kuzzle->setJwtToken(null);

        return $response['result'];
    }

    /**
     * Retrieves current user object.
     *
     * @param array $options (optional) arguments
     *
     * @return mixed
     */
    public function getCurrentUser(array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('auth', 'getCurrentUser'),
            [],
            $options
        );

        return $response['result'];
    }

    /**
     * Get all authentication strategies registered in Kuzzle
     *
     * @param array $options Optional parameters
     * @return array[]
     */
    public function getStrategies(array $options = [])
    {

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('auth', 'getStrategies'),
            [],
            $options
        );

        return $response['result'];
    }

    /**
     * Gets the rights of the current user
     *
     * @param array $options Optional parameters
     * @return array[]
     */
    public function getMyRights(array $options = [])
    {

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('auth', 'getMyRights'),
            [],
            $options
        );

        return $response['result']['hits'];
    }

    /**
     * Create credentials of the specified <strategy> for the current user.
     *
     * @param string $strategy
     * @param string $credentials
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function createMyCredentials($strategy, $credentials, array $options = [])
    {
        if (empty($strategy) || empty($credentials)) {
            throw new InvalidArgumentException('Kuzzle\Auth::createMyCredentials: Unable to create credentials: no strategy or no crendentials specified');
        }

        $options['httpParams'][':strategy'] = $strategy;

        return $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('auth', 'createMyCredentials'),
            [
                'body' => json_encode($credentials)
            ],
            $options
        )['result'];
    }

    /**
     * Delete credentials of the specified <strategy> for the current user.
     *
     * @param string $strategy
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function deleteMyCredentials($strategy, array $options = [])
    {
        if (empty($strategy)) {
            throw new InvalidArgumentException('Kuzzle\Auth::deleteMyCredentials: Unable to delete credentials: no strategy specified');
        }

        $options['httpParams'][':strategy'] = $strategy;

        return $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('auth', 'deleteMyCredentials'),
            [],
            $options
        )['result'];
    }

    /**
     * Check that the current user has credentials for the specified <strategy>.
     *
     * @param string $strategy
     * @param array $options
     *
     * @return boolean
     *
     * @throws InvalidArgumentException
     */
    public function credentialsExist($strategy, array $options = [])
    {
        if (empty($strategy)) {
            throw new InvalidArgumentException('Kuzzle\Auth::credentialsExist: Unable to check if credentials exist: no strategy specified');
        }

        $options['httpParams'][':strategy'] = $strategy;

        return $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('auth', 'credentialsExist'),
            [],
            $options
        )['result'];
    }

    /**
     * Get credential information of the specified <strategy> for the current user.
     *
     * @param string $strategy
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function getMyCredentials($strategy, array $options = [])
    {
        if (empty($strategy)) {
            throw new InvalidArgumentException('Kuzzle\Auth::getMyCredentials: Unable to get credentials: no strategy specified');
        }

        $options['httpParams'][':strategy'] = $strategy;

        return $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('auth', 'getMyCredentials'),
            [],
            $options
        )['result'];
    }

    /**
     * Update credentials of the specified <strategy> for the current user.
     *
     * @param string $strategy
     * @param string $credentials
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function updateMyCredentials($strategy, $credentials, array $options = [])
    {
        if (empty($strategy) || empty($credentials)) {
            throw new InvalidArgumentException('Kuzzle\Auth::updateMyCredentials: Unable to update credentials: no strategy specified');
        }

        $options['httpParams'][':strategy'] = $strategy;

        return $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('auth', 'updateMyCredentials'),
            [
                'body' => json_encode($credentials)
            ],
            $options
        )['result'];
    }

    /**
     * Validate credentials of the specified <strategy> for the current user.
     *
     * @param string $strategy
     * @param string $credentials
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function validateMyCredentials($strategy, $credentials, array $options = [])
    {
        if (empty($strategy) || empty($credentials)) {
            throw new InvalidArgumentException('Kuzzle\Auth::validateMyCredentials: Unable to validate credentials: no strategy specified');
        }

        $options['httpParams'][':strategy'] = $strategy;

        return $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('auth', 'validateMyCredentials'),
            [
                'body' => json_encode($credentials)
            ],
            $options
        )['result'];
    }


    /**
     * Performs a partial update on the current user.
     *
     * @param array $content a plain javascript object representing the user's modification
     * @param array $options (optional) arguments
     *
     * @return array
     */
    public function updateSelf(array $content, array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('auth', 'updateSelf'),
            [
                'body' => $content
            ],
            $options
        );

        return $response['result'];
    }
}
