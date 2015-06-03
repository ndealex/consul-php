<?php
/**
 * Created by PhpStorm.
 * User: aschrapel
 * Date: 29.05.2015
 * Time: 15:49
 */

namespace BR\Consul\Model;


use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseClassInterface;

class NodeList implements ResponseClassInterface {

    /**
     * @var Node[]
     */
    protected $list;

    /**
     * @param OperationCommand $command
     * @return NodeList
     */
    public static function fromCommand(OperationCommand $command){

        $response = json_decode($command->getResponse()->getBody(true), true);

        $nodeList = new self();

        foreach($response as $nd) {
            $node = new Node();
            $node->setId($nd['Node']);
            $node->setAddress($nd['Address']);

            $nodeList->list[$node->getId()]=$node;
        }

        return $nodeList;
    }

    /**
     * @return Node[]
     */
    public function toArray(){
        return $this->list;
    }

    /**
     * @param $id
     * @return Node
     */
    public function getNodeById($id){
        return $this->list[$id];
    }
}