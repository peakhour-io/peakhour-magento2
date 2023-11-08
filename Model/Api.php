<?php

namespace Peakhour\Cdn\Model;

use Peakhour\Cdn\Helper\Data;
use Laminas\Http\ClientFactory;
use Laminas\Http\HeadersFactory;
use Laminas\Http\Request;
use Laminas\Http\RequestFactory;

class Api
{

    const FLUSH_ALL = 'all';
    public const PURGE_TIMEOUT = 10;

    /**
     * @var Config $config,
     */
    private $config;

    /**
     * @var Data
     */
    private $helper;
	
    /**
     * @var ClientFactory
     */
    private $clientFactory;
	
    /**
     * @var RequestFactory
     */
    private $requestFactory;
	
    /**
     * @var HeadersFactory
     */
    private $headersFactory;
    
    /**
     * Api constructor.
     * @param Config $config
     * @param Data $helper
     * @param ClientFactory $clientFactory
     * @param RequestFactory $requestFactory
     * @param HeadersFactory $headersFactory
     */
    public function __construct(
        Config $config,
        Data $helper,
        ClientFactory        $clientFactory,
        RequestFactory       $requestFactory,
        HeadersFactory $headersFactory
    ) {
        $this->config = $config;
        $this->helper = $helper;
        $this->clientFactory = $clientFactory;
        $this->requestFactory = $requestFactory;
        $this->headersFactory = $headersFactory;
    }


    function makeRequestBody($urls, $inverse=false, $flushType=self::FLUSH_ALL, $purgeAll=false)
    {
        $paths = array();

        foreach ($urls as $url) {
            $parsed = parse_url( $url );
            $path = ( isset( $parsed['path'] ) ? $parsed['path'] : null );
            if ( empty( $path ) ) {
                $path = '/';
            }
            if ( isset( $parsed['query'] ) ) {
                $path = $path . '?' . $parsed['query'];
            }
            array_push($paths, $path );

        }
        $paths = array_values(array_unique($paths));

        return json_encode(array('paths' => $paths, 'inverse' => $inverse, 'flush_type' => $flushType, 'purge_all' => $purgeAll));
    }
    
    function doRequest($url, $method, $body=null, $apiKey=null)
    {
        if (is_null($apiKey)) {
            $apiKey = $this->config->getApiKey();
        }

        $headers = $this->headersFactory->create();

    	$headers->addHeaderLine('Accept', 'application/json');
    	$headers->addHeaderLine('Authorization', 'Bearer ' . $apiKey);
    	$headers->addHeaderLine('Content-Type', 'application/json');

        $this->helper->debug($method . ' ' . $url . ' ' . $body . '' . $apiKey);

        try {
            $client = $this->clientFactory->create();
            $client->setOptions([
                'timeout'      => self::PURGE_TIMEOUT,
                'httpversion' => '1.1'
            ]);
            $request = $this->requestFactory->create();

            $request->setUri($url);
            $request->setHeaders($headers);
            if (!is_null($body)) {
                $request->setContent($body);
            }
	    $request->setMethod($method);

            $response = $client->send($request);

            if (!$response->isOk()) {
                throw new \Exception('Error in response (' . $response->getStatusCode() . ') ' . $response->getBody());
            }

            return array('success' => true, 'error' => null, 'body' => $response->getBody());
        } catch (\Exception $e) {
            $this->helper->debug("Failed $method to $url: " . $e->getMessage());
            return array('success' => false, 'error' => $e->getMessage());
        }
    }
    
    function getResourcesUrl($domain=null)
    {
        if (is_null($domain)) {
            $domain = $this->config->getDomain();
        }
        return $this->config->getApiUrl()    . 'domains/' . $domain . '/services/rp/cdn/resources';
    }

    function getTagsUrl($domain=null)
    {
        if (is_null($domain)) {
            $domain = $this->config->getDomain();
        }
        return $this->config->getApiUrl() . 'domains/' . $domain . '/services/rp/cdn/tag';
    }

    function doResourcesRequest($method, $urls)
    {
        $resourcesUrl = $this->getResourcesUrl();
        $body = $this->makeRequestBody($urls);
        $this->doRequest($resourcesUrl, $method, $body);
    }

    public function cacheResource($url)
    {
        $this->cacheResources(array($url));
    }

    public function cacheResources($urls)
    {
            $this->doResourcesRequest('POST', $urls);
    }

    public function purgeAll($flushType = self::FLUSH_ALL)
    {
        $resourcesUrl = $this->getResourcesUrl();
        $body = $this->makeRequestBody([], false, $flushType, true);
        return $this->doRequest($resourcesUrl, 'DELETE', $body);
    }

    public function purgeWildcard($url)
    {
        $resourcesUrl = $this->getResourcesUrl();
        $body = $this->makeRequestBody((array)$url);
        return $this->doRequest($resourcesUrl, 'DELETE', $body);
    }

    public function purgeResource($url)
    {
        $this->purgeResources(array($url));
    }

    public function purgeResources($urls)
    {
        $this->doResourcesRequest('DELETE', $urls);
    }

    public function purgeTags($tags)
    {
        $this->doRequest($this->getTagsUrl(),'DELETE', json_encode(array('tags' => $tags)));
    }


    public function testConnection($apiKey, $domain)
    {
        $resourcesUrl = $this->getResourcesUrl($domain);
        return $this->doRequest($resourcesUrl, 'OPTIONS', null, $apiKey);
    }
}
