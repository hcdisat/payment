<?php namespace HcDisat\Payment\Core;

use Traversable;

class ItemBag implements \IteratorAggregate, \Countable
{
    /**
     * @var array
     */
    protected $items;

    /**
     * ItemBag constructor.
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->replace($items);
    }

    /**
     * @param array $items
     */
    public function replace(array $items = [])
    {
        $this->items = [];
        array_walk($items, function($item){
            $this->add($item);
        });
    }

    /**
     * @param Item $item
     */
    public function add($item)
    {
        $this->items[] = $item instanceof Item
            ? $item : new Item($item);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->items);
    }
}