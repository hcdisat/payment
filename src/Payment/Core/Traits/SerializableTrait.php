<?php namespace HcDisat\Payment\Core\Traits;

trait SerializableTrait
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->parameters->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return $this->parameters->toJson($options);
    }
}