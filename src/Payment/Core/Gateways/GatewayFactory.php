<?php namespace HcDisat\Payment\Gateways;


use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class GatewayFactory
{
    
    /**
     * @param string $name
     * @param ClientInterface $httpClient
     * @param HttpRequest|null $httpRequest
     * @return mixed
     */
    public function create(string $name, ClientInterface $httpClient = null, HttpRequest $httpRequest = null)
    {
        if( !array_key_exists($name, $gateways = $this->getSupportedGateways()) ){
            throw new \RuntimeException("{$name} is no a valid gateway.");
        }

         $class = $gateways[$name];

        if( !class_exists($class) ){
            throw new \RuntimeException("Class {$class} not found.");
        }

        return new $class($httpClient, $httpRequest);
    }

    /**
     * gets all suported gateways
     * @return array
     */
    private function getSupportedGateways()
    {
        $gateways = [];

        foreach (config('payment.gateways') as $key => $value) {
            $gateways[$value['name']] = $value['class'];
        }

        return $gateways;
    }
}