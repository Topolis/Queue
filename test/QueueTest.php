<?php
/**
 * Created by PhpStorm.
 * User: bulla
 * Date: 08.06.17
 * Time: 11:44
 */

namespace Topolis\Queue;

use Topolis\Queue\Queue;

class QueueTest extends \PHPUnit_Framework_TestCase {

    public function testSimpleAdd() {
        $queue = new Queue();

        $queue->add("AI");
        $queue->add("BI");
        $queue->add("CI");

        $res = [];
        foreach($queue as $item){
            $res[] = $item;
        }

        $this->assertSame(["AI", "BI", "CI"], $res, "Simple add");
    }

    public function testIterator() {
        $queue = new Queue();

        $queue->add("AI");
        $queue->add("BI");
        $queue->add("CI");

        $this->assertEquals("AI", $queue->rewind());
        $this->assertEquals("AI", $queue->current());
        $this->assertEquals("AI", $queue->key());
        $this->assertEquals(true, $queue->valid());

        $this->assertEquals("BI", $queue->next());
        $this->assertEquals("BI", $queue->current());
        $this->assertEquals("BI", $queue->key());
        $this->assertEquals(true, $queue->valid());

        $this->assertEquals("CI", $queue->next());
        $this->assertEquals("CI", $queue->current());
        $this->assertEquals("CI", $queue->key());
        $this->assertEquals(true, $queue->valid());

        $this->assertEquals(null, $queue->next());
        $this->assertEquals(null, $queue->current());
        $this->assertEquals(null, $queue->key());
        $this->assertEquals(false, $queue->valid());

        $this->assertEquals("AI", $queue->rewind());
    }

    public function testObjectsAdd() {
        $queue = new Queue();

        $A = new A("classA1");
        $B = new B("classB1");
        $C = new A("classA2");

        $queue->add($A);
        $queue->add($B);
        $queue->add($C);

        $res = [];
        foreach($queue as $item){
            $res[] = $item;
        }

        $this->assertSame([$A,$B,$C], $res, "Objects add");
    }

    public function testAfterAdd() {
        $queue = new Queue();

        $queue->add("AI");
        $queue->add("BI");
        $queue->add("CI");

        $queue->addAfter("AAI", "AI");
        $queue->addAfter("BBI", "BI");
        $queue->addAfter("CCI", "CI");
        $queue->addAfter("BBBI", "BBI");

        $res = [];
        foreach($queue as $item){
            $res[] = $item;
        }

        $this->assertSame(["AI", "AAI", "BI", "BBI", "BBBI", "CI", "CCI"], $res, "Simple add");
    }

    public function testBeforeAdd() {
        $queue = new Queue();

        $queue->add("AI");
        $queue->add("BI");
        $queue->add("CI");

        $queue->addBefore("AAI", "AI");
        $queue->addBefore("BBI", "BI");
        $queue->addBefore("CCI", "CI");
        $queue->addBefore("BBBI", "BBI");

        $res = [];
        foreach($queue as $item){
            $res[] = $item;
        }

        $this->assertSame(["AAI", "AI", "BBBI", "BBI", "BI", "CCI", "CI"], $res, "Simple add");
    }

    public function testPrioritiesAdd() {
        $queue = new Queue();

        $queue->add("AI", 100);
        $queue->add("BI", 300);
        $queue->add("CI", 200);
        $queue->add("DI", 10);
        $queue->add("EI", 300);

        $res = [];
        foreach($queue as $item){
            $res[] = $item;
        }

        $this->assertSame(["DI", "AI", "CI", "BI", "EI"], $res, "Priorities add");

        // Test set priorities
        $reflection = new \ReflectionClass(get_class($queue));
        $prop = $reflection->getProperty("order");
        $prop->setAccessible(true);
        $ordered = $prop->getValue($queue);

        $this->assertSame(["DI" => 10, "AI" => 100, "CI" => 200, "BI" => 300, "EI" => 300], $ordered, "Priorities set");
    }

    public function testHas() {
        $queue = new Queue();

        $A = new A("classA1");
        $B = new B("classB1");
        $C = "ABC";
        $D = new A("classC1");
        $E = "ABD";

        $queue->add($A);
        $queue->add($B);
        $queue->add($C);

        $this->assertNotFalse( $queue->has($A) );
        $this->assertNotFalse( $queue->has($B) );
        $this->assertNotFalse( $queue->has($C) );

        $this->assertFalse( $queue->has($D) );
        $this->assertFalse( $queue->has($E) );
    }

}

class A {
   public $id = null;
   public function __construct($id){
       $this->id = $id;
   }
};

class B {
    public $id = null;
    public function __construct($id){
        $this->id = $id;
    }
};