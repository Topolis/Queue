<?php
/**
 * An example of running a queue inside a silex application
 * In this example the queue and all it's items will only be loaded lazily if someone actually accesses the queue.
 *
 * Dont forget to require silex/silex before running this sample!
 *
 * Can be run on command line:
 *     php silex.php
 */

require '../vendor/autoload.php';

use Silex\Application;
use Topolis\Queue\Queue;
use Symfony\Component\HttpFoundation\Request;

// The silex test application
$app = new Application();
$app['debug'] = true;

// Adding the Queue to your silex application with lazy loading
$app["queue"] = function(){
    echo "Init Queue\n";
    return new Queue();
};

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
$app->extend("queue", function(Queue $queue, $app){
    $queue->add(new TestItem("A"));
    $queue->add(new TestItem("B"));
    return $queue;
});

// Our test view - If you comment out the foreach loop, you will see that neither queue nor items are initialized - lazy yay!
$app->get("/", function(Silex\Application $app){
    echo "Executing application\n";
    foreach($app['queue'] as $item){
        $item->do();
    }
    return new \Symfony\Component\HttpFoundation\Response();
});

// TEST: And a simple hack to test the application from command line
$request = Request::create("/", "GET");
echo "Initializing application\n";
$app->run($request);
