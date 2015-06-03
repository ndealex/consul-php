<?php

namespace BR\Consul\Model;

use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseClassInterface;

class Service implements ResponseClassInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string[]
     */
    protected $tags;


    /**
     * @var Node[]
     */
    protected $nodes;


    /**
     * @var Check
     */
    protected $check;

    public static function fromCommand(OperationCommand $command)
    {

        $response = json_decode($command->getResponse()->getBody(true), true);

        $service = new self();

        foreach($response as $node) {
            $nd = new Node();
            $nd->setId($node['Node']['Node']);
            $nd->setAddress($node['Node']['Address']);

            if($service->getId() == null) {
                $service->setId($node['Service']['ID']);
                $service->setName($node['Service']['Service']);
                $service->setTags($node['Service']['Tags']);
                $service->setPort($node['Service']['Port']);
            }

            foreach($node['Checks'] as $chk) {
                $check = new Check();
                $check->setId($chk['CheckID']);
                $check->setName($chk['Name']);
                $check->setNodeId($nd->getId());
                $check->setStatus($chk['Status']);
                $check->setNotes($chk['Notes']);
                $check->setOutput($chk['Output']);
                $check->setServiceId($service->getId());
                $check->setServiceName($service->getName());
                $nd->addCheck($check);
                if($check->getId() == 'service:'.$service->getId()){
                    $service->setCheck($check);
                }
            }

            $service->addNode($nd);
        }
        return $service;
    }

    /**
     * @return Check
     */
    public function getCheck()
    {
        return $this->check;
    }

    /**
     * @param Check $check
     */
    public function setCheck(Check $check)
    {
        $this->check = $check;
    }

    /**
     * @return Node[]
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * @param Node[] $nodes
     */
    public function setNodes($nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * @param Node $node
     */
    public function addNode(Node $node){
        $this->nodes[$node->getId()]=$node;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
    }

    /**
     * @param string $tag
     */
    public function addTag($tag)
    {
        $this->tags[] = $tag;
    }
} 
