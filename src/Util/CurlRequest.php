<?php

namespace Kuzzle\Util;

/**
 * Class CurlRequest
 * @package Kuzzle\Util
 */
class CurlRequest implements RequestInterface
{
    /**
     * @param array $parameters
     * @return array
     */
    public function execute(array $parameters = [])
    {
        $url = '';
        $method = '';
        $headers = [];
        $body = '';
        $queryParameters = array_key_exists('query_parameters', $parameters) ? $parameters['query_parameters'] : [];

        if (array_key_exists('body', $parameters)) {
            $body = $parameters['body'];
        }

        if (array_key_exists('url', $parameters)) {
            $url = $parameters['url'];
        }

        if (count($queryParameters) > 0) {
            $url.= '?' . http_build_query($queryParameters);
        }

        if (array_key_exists('method', $parameters)) {
            $method = $parameters['method'];
        }

        if (array_key_exists('headers', $parameters)) {
            $headers = $parameters['headers'];
        }

        $curlResource = curl_init();

        if (!empty($body)) {
            curl_setopt($curlResource, CURLOPT_POSTFIELDS, $body);
        }

        curl_setopt_array($curlResource, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
        ]);

        /**
         * @todo: handle http proxy via options
         */

        $result = [
            'response' => curl_exec($curlResource),
            'error' => curl_error($curlResource)
        ];

        curl_close($curlResource);

        return $result;
    }
}
