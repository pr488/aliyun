<?php

/*
 * This file is part of the pr488/aliyun.
 *
 * (c) pr488 <pr488@hotmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Aliyun\Core;

use GuzzleHttp\Client;

/**
 * Class BaseClient.
 */
class BaseClient
{
    /**
     * @var ServiceContainer
     */
    protected $app;

    /**
     * @var String
     */
    protected $accessKeyId;

    /**
     * @var String
     */
    protected $accessKeySecret;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $format = 'JSON';

    /**
     * @var string
     */
    protected $regionId = 'cn-hangzhou';

    /**
     * @var string
     */
    protected $version;

    /**
     * BaseClient constructor.
     *
     * @param serviceContainer $app
     */
    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
    }

    /**
     * GET request.
     *
     * @param string $url
     * @param array  $query
     *
     * @return \Aliyun\Core\Support\Collection|array|object|string
     */
    public function httpGet(string $url, array $query = [])
    {
        return $this->request($url, 'GET', ['query' => $query]);
    }

    /**
     * POST request.
     *
     * @param string $url
     * @param array  $data
     *
     * @return \Aliyun\Core\Support\Collection|array|object|string
     */
    public function httpPost(string $url, array $data = [])
    {
        return $this->request($url, 'POST', ['form_params' => $data]);
    }

    /**
     * JSON request.
     *
     * @param string       $url
     * @param string|array $data
     * @param array        $query
     *
     * @return \Aliyun\Core\Support\Collection|array|object|string
     */
    public function httpPostJson(string $url, array $data = [], array $query = [])
    {
        return $this->request($url, 'POST', ['query' => $query, 'json' => $data]);
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $options
     *
     * @return \Aliyun\Core\Support\Collection|array|object|string
     */
    public function request(string $url, string $method = 'GET', array $options = [])
    {
        $param              = $method == 'GET' ? $options['query'] : $options['form_params'];
        $param              = array_merge([
            'Format'           => $this->format,
            'RegionId'         => $this->regionId,
            'Version'          => $this->version,
            'AccessKeyId'      => $this->accessKeyId,
            'SignatureMethod'  => 'HMAC-SHA1',
            'SignatureNonce'   => md5(uniqid(mt_rand(), true)),
            'SignatureVersion' => '1.0',
            'Timestamp'        => gmdate('Y-m-d\TH:i:s\Z'),
        ], $param);
        $signature          = $this->computeSignature($param, $this->accessKeySecret);
        $param['Signature'] = $signature;
        if ($method == 'GET') {
            $options['query'] = $param;
        } else {
            $options['form_params'] = $param;
        }

        try {
            $response = $this->getHttpClient()->request($method, $url, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Return GuzzleHttp\Client instance.
     *
     * @return Client
     */
    public function getHttpClient(): Client
    {
        if (!($this->httpClient instanceof Client)) {
            $this->httpClient = $this->app['httpClient'] ?? new Client();
        }
        return $this->httpClient;
    }

    protected function computeSignature($parameters, $accessKeySecret)
    {
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach ($parameters as $key => $value) {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($value);
        }
        $stringToSign = 'POST' . '&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));
        $signature    = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . "&", true));
        return $signature;
    }

    protected function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }
}
