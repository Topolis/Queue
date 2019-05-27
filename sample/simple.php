<?php
/**
 * A very simple example of a queue
 * Can be run on command line:
 *     php simple.php
 */

require '../vendor/autoload.php';
use Topolis\Queue\Queue;

$queue = new Queue();

// A simple test item to be added to the queue
class TestItem {
    public function __construct($id){
        echo "Init $id Item\n";
    }
    public function do(){
        echo "Item is done\n";
    }
};

// Adding an item to the queue with lazy loading
$queue->add(new TestItem("A"));
$queue->add(new TestItem("B"));

// Executing queue
foreach($queue as $item){
    $item->do();
}

