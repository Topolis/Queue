# Queue
A simple queueing system. You can add items into a list and order them by priorities or before/after other items.
priorities or item keys dont need to be unique.

Note that the keys of an object might get a unique suffix if it is already taken

#### Sample
You can find samples inside the folder *sample*
 - *silex.php* Demonstrating queue usage and lazy loading inside a silex application
 - *simple* A simple stand-alone queue usage


```
$queue = new Queue();

// Add item at priority
$queue->add($item, Queue::EARLY);

// Add item with specific key at priority
$queue->add($item, Queue::EARLY, "myItem");

// Add item before other item in queue
$queue->addBefore($item, $otherItem);

// Add item before key of other item in queue
$queue->addAfter($item, "otherItem");

// Check if item with key is in queue
$check = $queue->has("myItem");

// Check if item is in queue
$check = $queue->has($item);

// Iterate through queue in order
foreach($queue as $item){
    $item->doSomething()
}
```
