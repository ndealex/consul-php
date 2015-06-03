<?php
/**
 * Created by PhpStorm.
 * User: aschrapel
 * Date: 29.05.2015
 * Time: 14:37
 */

namespace BR\Consul\Model;


class Key {

    /**
     * @var string
     */
    protected $key;

    public function __construct($name){
        $this->key = $name;
    }

    public function getName(){
        return $this->key;
    }

    public function isFolder(){
        if (strrpos($this->name, '/') === '0') {
            return true;
        }
        return false;
    }

    public function __toString(){
        return $this->key;
    }
}