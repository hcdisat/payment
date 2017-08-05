<?php namespace HcDisat\Payment\Core;

use HcDisat\Payment\Core\Traits\GetterAndSetterTrait;
use HcDisat\Payment\Core\Traits\SerializableTrait;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Class Item
 * @package HcDisat\Payment
 * @property string $name
 * @property string $description
 * @property int $quantity
 * @property float $price
 */
class Item implements Arrayable, Jsonable
{
    use GetterAndSetterTrait, SerializableTrait;

    /**
     * Item constructor.
     * @param array $parameters
     */
    public function __construct(array $parameters= null)
    {
        $this->initialize($parameters);
    }
}