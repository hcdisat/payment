<?php namespace HcDisat\Payment\Core\Traits;

use HcDisat\Payment\Core\Exceptions\InvalidCreditCardException;
use Illuminate\Support\Collection;

trait GetterAndSetterTrait
{
    /**
     * Internal storage of all of the card parameters.
     *
     * @var Collection
     */
    protected $parameters;

    /**
     * Initialize the object with parameters.
     *
     * If any unknown parameters passed, they will be ignored.
     *
     * @param array $parameters An associative array of parameters
     * @return $this provides a fluent interface.
     */
    public function initialize(array $parameters = null)
    {
        $this->parameters = app(Collection::class);

        if( !is_null($parameters) ) {

            foreach ($parameters as $key => $value) {
                $this->parameterSetter(camel_case($key), $value);
            }
        }

        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     * @throws InvalidCreditCardException
     */
    public function parameterSetter($name, $value)
    {

        $this->parameters->put($name, $value);

        return $this;
    }

    /**
     * @param string $parameter
     * @return mixed|null
     */
    public function parameterGetter(string $parameter)
    {
        if( $this->parameters->isEmpty() && !$this->parameters->has($parameter) )
            return null;

        return $this->parameters->get($parameter);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if( property_exists($this, $name) ){
            $this->$name = $value;
        }
        elseif(method_exists($this, $method = 'set'.ucfirst($name)) )
        {
            $this->$method($value);
            return;
        }

        $this->parameterSetter($name, $value);
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if( property_exists($this, $name) )
            return $this->$name;

        return $this->parameterGetter($name);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed|null
     */
    public function __call($name, $arguments)
    {
        if( method_exists($this, $name) ){
            return $this->$name($arguments);
        }

        if( strtolower(substr($name, 0, 3)) == 'get' ){
            $property = substr($name, 3);
            $property = strtolower(substr($property, 0, 1)).substr($property, 1);

            return $this->parameterGetter($property);
        }

        return null;
    }

    /**
     * get parameters
     * @var Collection
     * @return Collection
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}