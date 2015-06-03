<?php

namespace BR\Consul;

use Guzzle\Http\Message\Request;
use Guzzle\Service\Client as GuzzleClient;
use Guzzle\Service\Description\ServiceDescription;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractClient
{
    /**
     * @var GuzzleClient
     */
    protected $client;

    /**
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->client = new GuzzleClient($baseUrl);
        $this->client->setDescription(ServiceDescription::factory(__DIR__ . '/config/service.json'));
        $this->client->getEventDispatcher()->addListener('client.create_request', function ($event){
            /** @var Request $request */
            $request = $event['request'];
            $request->getQuery()->useUrlEncoding(false);
        });
    }

    /**
     * @param EventSubscriberInterface $plugin
     */
    public function addGuzzlePlugin(EventSubscriberInterface $plugin)
    {
        $this->client->addSubscriber($plugin);
    }
} 
