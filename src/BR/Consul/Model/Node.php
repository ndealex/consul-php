<?php
/**
 * Created by PhpStorm.
 * User: aschrapel
 * Date: 29.05.2015
 * Time: 15:30
 */

namespace BR\Consul\Model;


use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseClassInterface;

class Node implements ResponseClassInterface {

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var Service[]
     */
    protected $serviceList = [];

    /**
     * @var Check[]
     */
    protected $checkList = [];

    /**
     * @param OperationCommand $command
     * @return Node
     */
    public static function fromCommand(OperationCommand $command){

        $response = json_decode($command->getResponse()->getBody(true), true);

        $node = new self();

        $node->id = $response['Node'];
        $node->address = $response['Address'];

        foreach($response['Services'] as $srv) {
            $service = new Service();
            $service->setId($srv['ID']);
            $service->setName($srv['Service']);
            $service->setTags($srv['Tags']);
            $service->setPort($srv['Port']);
            $node->serviceList[$service->getId()]=$service;
        }

        return $node;
    }

    /**
     * @param Check $check
     */
    public function addCheck(Check $check){
        $this->checkList[$check->getId()]=$check;
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
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return Service[]
     */
    public function getServiceList()
    {
        return $this->serviceList;
    }

    /**
     * @param Service[] $serviceList
     */
    public function setServiceList($serviceList)
    {
        $this->serviceList = $serviceList;
    }

    /**
     * Check the Status of all known checks.
     *
     * @return string Check::STATE_*
     */
    public function getStatus(){
        $critical=0;
        $warning=0;
        $unknown=0;
        foreach($this->checkList as $check){
            switch($check->getStatus()){
                case Check::STATE_UNKNOWN:
                    $unknown++;
                    break;
                case Check::STATE_WARNING:
                    $warning++;
                    break;
                case Check::STATE_CRITICAL:
                    $critical++;
                    break;
            }
        }
        if($critical>0){
            return Check::STATE_CRITICAL;
        }
        if($warning>0 || $unknown>0){
            return Check::STATE_WARNING;
        }
        return Check::STATE_PASSING;
    }

}