<?php

namespace BR\Consul;

use BR\Consul\Exception\NotFoundException;
use BR\Consul\Model\DatacenterList;
use BR\Consul\Model\Node;
use BR\Consul\Model\Service;
use BR\Consul\Model\ServiceList;
use Zend\Debug\Debug;

class Agent extends AbstractClient
{
    /**
     * Gets a Node from the catalog
     *
     * @link https://www.consul.io/docs/agent/http/catalog.html#catalog_nodes
     *
     * @param  string            $node
     * @param  string|null       $datacenter
     * @throws NotFoundException
     * @return Node
     */
    public function getNode($node, $datacenter = null)
    {
        $command = $this->client->getCommand('GetNode', ['node' => $node, 'datacenter' => $datacenter,]);
        try {
            $result = $command->execute();
        } catch (ClientErrorResponseException $e) {
            throw new NotFoundException();
        }

        return $result;
    }

    /**
     * Gets a Nodes from the catalog
     *
     * @link https://www.consul.io/docs/agent/http/catalog.html#catalog_nodes
     *
     * @param  string            $node
     * @param  string|null       $datacenter
     * @throws NotFoundException
     * @return NodeList
     */
    public function getNodes($datacenter = null)
    {
        $command = $this->client->getCommand('GetNodes', ['datacenter' => $datacenter,]);
        try {
            $result = $command->execute();
        } catch (ClientErrorResponseException $e) {
            throw new NotFoundException();
        }

        return $result;
    }

    /**
     * Registers a service with the local agent.
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_15
     *
     * @param  Service $service
     * @return boolean
     */
    public function registerService(Service $service)
    {
        $command = $this->client->getCommand(
            'AgentServiceRegister',
            [
                'Name' => $service->getName(),
                'ID' => $service->getId(),
                'Port' => $service->getPort(),
                'Tags' => $service->getTags(),
            ]
        );
        $result = $command->execute();

        return $result->isSuccessful();
    }

    /**
     * Returns a list of services that are currently registered with the local agent
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_6
     *
     * @return ServiceList
     */
    public function getServices()
    {
        $command = $this->client->getCommand('AgentGetServices');
        $result = $command->execute();

        return $result;
    }

    /**
     * Removes a service from the local agent.
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_16
     *
     * @param  string $serviceId
     * @return boolean
     */
    public function deregisterService($serviceId)
    {
        $command = $this->client->getCommand('AgentServiceDeregister', ['serviceId' => $serviceId]);

        $result = $command->execute();

        return $result->isSuccessful();
    }

    /**
     * Removes a service from the local agent.
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_16
     * @see Client::deregisterService()
     *
     * @param Service $service
     * @return bool
     */
    public function removeService(Service $service)
    {
        return $this->deregisterService($service->getId());
    }

    /**
     * Gets a Service with their nodes and checks info from the catalog
     *
     * @link https://www.consul.io/docs/agent/http/health.html#health_service
     *
     * @param  string            $service
     * @param  string|null       $datacenter
     * @param  string|null       $tag
     * @throws NotFoundException
     * @return Node
     */
    public function getService($service, $datacenter = null, $tag = null)
    {
        $command = $this->client->getCommand('getHealthService', ['service' => $service, 'datacenter' => $datacenter, 'tag' => $tag, ]);
        try {
            $result = $command->execute();
        } catch (ClientErrorResponseException $e) {
            throw new NotFoundException();
        }
        return $result;
    }

    /**
     * Retrieves the list of datacenters known by Consul.
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_26
     *
     * @return DatacenterList
     */
    public function getDatacenters()
    {
        $command = $this->client->getCommand('AgentCatalogGetDatacenters');

        $result = $command->execute();

        return $result;
    }
} 
