<?php

namespace Topolis\Queue;

use ArrayAccess;
use Iterator;

class Queue implements Iterator {

    const EARLIEST = 0;
    const EARLY = 100;
    const NORMAL = 500;
    const LATE = 900;
    const LATEST = 1000;

    /* @var array $queue */
    protected $items = [];
    protected $order = [];

    public function  __construct(){
    }

    /**
     * Add item into the queue
     * @param mixed $item
     * @param int $priority
     * @param null $key
     * @return null|string
     */
    public function add($item, $priority = self::NORMAL, $key = null){
        // Search last item with same priority
        $index = array_reverse($this->order);
        $reference = array_search($priority, $index, true);

        if($reference)
            return $this->addAfter($item, $reference, $key);

        // f nothing found, find closest with lesser priority
        $key = $this->generateKey($key ?? $item);
        foreach($this->order as $reference => $refPriority){
            if($refPriority > $priority) {
                $position = array_search($reference, array_keys($this->order), true);
                $this->insertAt($key, $item, $position, $priority);
                break;
            }
        }

        // Nothing with lesser priority found -> add to end
        $this->items[$key] = $item;
        $this->order[$key] = $priority;

        return $key;
    }

    public function has($reference){

        // Search by object
        if($key = array_search($reference, $this->items, true))
            return $key;

        // Search by key
        if(!\is_object($reference) && array_key_exists($reference, $this->items))
            return $reference;

        return false;
    }

    /**
     * Add item before other item
     * @param $item
     * @param $reference
     * @param null $key
     * @return null|string
     */
    public function addBefore($item, $reference, $key = null){
        $key = $this->generateKey($key ?? $item);

        //Search by item
        if($index = array_search($reference, $this->items, true))
            $reference = $index;

        // search by key
        if(array_key_exists($reference, $this->order)){
            $position = array_search($reference, array_keys($this->order), true);
            $priority = $this->order[$reference];
            $this->insertAt($key, $item, $position, $priority);
        } else {
            $this->insertAt($key, $item, 0, self::EARLY);
        }

        return $key;
    }

    public function addAfter($item, $reference, $key = null){
        $key = $this->generateKey($key ?? $item);

        //Search by item
        if($index = array_search($reference, $this->items, true))
            $reference = $index;

        // search by key
        if(array_key_exists($reference, $this->order)){
            $position = array_search($reference, array_keys($this->order), true);
            $priority = $this->order[$reference];
            $this->insertAt($key, $item, $position+1, $priority);
        } else {
            $this->insertAt($key, $item, \count($this->order), self::LATE);
        }

        return $key;
    }

    /* Iterrator Interface */

    public function current(){
        $key = key($this->order);
        return $key ? $this->items[$key] : null;
    }

    public function next(){
        next($this->order);
        return $this->current();
    }

    public function key(){
        return key($this->order);
    }

    public function valid(){
        return key($this->order) !== null;
    }

    public function rewind(){
        reset($this->order);
        return $this->current();
    }

    /* Internals */

    protected function generateKey($item, $allowDuplicate = false){

        if(is_numeric($item) || is_string($item))
            $key = (string) $item;
        else
            $key = md5(serialize($item));

        if($allowDuplicate)
            return $key;

        while(array_key_exists($key, $this->order)){
            $key = $key."_".uniqid('', true);
        }

        return $key;
    }

    /**
     * inserts $item with $key into ordered array at position $position
     * @param string $key
     * @param mixed $item
     * @param int $position
     */
    protected function insertAt($key, $item, $position, $priority) : void{

        $position = min(\count($this->order)+1, max(0, $position));

        $this->items[$key] = $item;

        $this->order =
            \array_slice($this->order, 0, $position, true) +
            array($key => $priority) +
            \array_slice($this->order, $position, \count($this->order), true);
    }
}