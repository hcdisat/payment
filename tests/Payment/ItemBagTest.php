<?php

use HcDisat\Payment\Tests\PaymentTestCase;

class ItemBagTest extends PaymentTestCase
{
    /**
     * @var array Item
     *
     */
    protected $items;

    public function setUp()
    {
        parent::setUp();

        $this->items = [];
        foreach (range(1, 5) as $index) {
            $this->items[] = $this->getMockBuilder(\HcDisat\Payment\Core\Item::class)
                ->getMock();
        }
    }

    public function testInstance()
    {
        $itemBag = new \HcDisat\Payment\Core\ItemBag($this->items);
        $this->assertNotNull($itemBag);
    }

    public function testAll()
    {
        $itemBag = new \HcDisat\Payment\Core\ItemBag($this->items);
        
        array_map(function($item){
            $this->assertInstanceOf(\HcDisat\Payment\Core\Item::class, $item);
            $this->assertNotNull($item);
        }, $itemBag->all());

        $this->assertCount(5, $itemBag->all());
    }

    public function testIterator()
    {
        $itemBag = new \HcDisat\Payment\Core\ItemBag($this->items);

        $this->assertInstanceOf(ArrayIterator::class, $itemBag->getIterator());

        foreach ($itemBag as $n => $item) {
            $this->assertInstanceOf(\HcDisat\Payment\Core\Item::class, $item);
        }
    }

    public function testCount()
    {
        $itemBag = new \HcDisat\Payment\Core\ItemBag($this->items);
        $this->assertCount(5, $itemBag);
    }
}
