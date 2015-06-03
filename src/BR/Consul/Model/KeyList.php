<?php

namespace BR\Consul\Model;

use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseClassInterface;

class KeyList implements ResponseClassInterface
{
    /**
     * @var Key[] $keys
     */
    protected $keys = [];

    /**
     * @param OperationCommand $command
     * @return KeyList
     */
    public static function fromCommand(OperationCommand $command)
    {
        $response = json_decode($command->getResponse()->getBody(true), true);

        $keyList = new self();
        foreach($response as $key) {
            $keyList->keys[] = new Key($key);
        }

        return $keyList;
    }

    /**
     * @return Key[]
     */
    public function toArray() {
        return $this->keys;
    }

}
