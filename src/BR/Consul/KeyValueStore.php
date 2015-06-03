<?php

namespace BR\Consul;

use BR\Consul\Exception\NotFoundException;
use BR\Consul\Model\KeyValue;
use BR\Consul\Model\KeyList;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Message\Request;

class KeyValueStore extends AbstractClient
{
    /**
     * Gets a value from the key/value store
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_3
     *
     * @param  string            $key
     * @param  string|null       $datacenter
     * @throws NotFoundException
     * @return KeyValue
     */
    public function getValue($key, $datacenter = null)
    {
        $command = $this->client->getCommand('GetValue', ['key' => $key, 'datacenter' => $datacenter,]);
        try {
            $result = $command->execute();
        } catch (ClientErrorResponseException $e) {
            throw new NotFoundException();
        }

        return $result;
    }

    /**
     * Gets a list of sub keys from the key/value store
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_3
     *
     * @param  string            $key
     * @param  string|null       $datacenter
     * @throws NotFoundException
     * @return KeyList
     */
    public function getList($key, $datacenter = null)
    {
        $command = $this->client->getCommand('LS', ['key' => $key, 'keys' => 1, 'datacenter' => $datacenter,]);
        try {
            $result = $command->execute();
        } catch (ClientErrorResponseException $e) {
            throw new NotFoundException();
        }

        return $result;
    }

    /**
     * Sets a value in the key/value store
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_3
     *
     * @param  string      $key
     * @param  string      $value
     * @param  string|null $datacenter
     * @return mixed
     */
    public function setValue($key, $value, $datacenter = null)
    {
        $command = $this->client->getCommand(
            'SetValue',
            [
                'key' => $key,
                'value' => $value,
                'datacenter' => $datacenter,
            ]
        );
        $command->prepare();

        $result = $command->execute();

        return $result;
    }

    /**
     * Deletes a value from the key/value store.
     *
     * @link http://www.consul.io/docs/agent/http.html#toc_3
     *
     * @param  string      $key
     * @param  string|null $datacenter
     * @return mixed
     */
    public function deleteValue($key, $datacenter = null)
    {
        $command = $this->client->getCommand(
            'DeleteValue',
            [
                'key' => $key,
                'datacenter' => $datacenter,
            ]
        );
        $result = $command->execute();

        return $result;
    }

    public function onClientCreateRequest(Event $event)
    {
        $request = $event['request'];
        if (empty($request)) {
            return;
        }

        $url = $request->getUrl(true);
        if (empty($url)) {
            return;
        }

        $path = $url->getPath();
        $path = rawurldecode($path);
        $path = str_replace('//', '/', $path);

        //$url->setPath($path);
        $request->setPath($path);
    }
} 
