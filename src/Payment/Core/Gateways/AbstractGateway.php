<?php namespace HcDisat\Payment\Gateways;

use HcDisat\Payment\Core\Contracts\GatewayContract;
use HcDisat\Payment\Core\PayOperationsContract;
use HcDisat\Payment\Core\Requests\AbstractRequest;
use HcDisat\Payment\Core\Traits\GetterAndSetterTrait;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use GuzzleHttp\Client as HttpClient;

abstract class AbstractGateway implements GatewayContract, PayOperationsContract
{
    use GetterAndSetterTrait;

    /**
     * @var \Guzzle\Http\ClientInterface
     */
    protected $httpClient;

    /**
     * @var HttpRequest
     */
    protected $httpRequest;

    /**
     * Create a new gateway instance
     *
     * @param ClientInterface $httpClient  A Guzzle client to make API calls with
     * @param HttpRequest     $httpRequest A Symfony HTTP request object
     */
    public function __construct(ClientInterface $httpClient = null, HttpRequest $httpRequest = null)
    {
        $this->httpClient = $httpClient ?? $this->getDefaultHttpClient();
        $this->httpRequest = $httpRequest ?? $this->getDefaultHttpRequest();
        $this->initialize();
    }

    /**
     * @param $class
     * @param array $parameters
     * @return mixed
     */
    protected function createRequest($class, array $parameters)
    {
        /** @var AbstractRequest $obj */
        $obj = new $class($this->httpClient, $this->httpRequest);

        return $obj->initialize(
            array_replace($this->getParameters()->toArray(), $parameters)
        );
    }

    /**
     * Get the global default HTTP client.
     *
     * @return HttpClient
     */
    protected function getDefaultHttpClient()
    {
        return new HttpClient('', [
            'curl.options' => [CURLOPT_CONNECTTIMEOUT => 60]
        ]);
    }

    /**
     * Get the global default HTTP request.
     *
     * @return HttpRequest
     */
    protected function getDefaultHttpRequest()
    {
        return HttpRequest::createFromGlobals();
    }
}

