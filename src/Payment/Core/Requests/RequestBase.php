<?php namespace HcDisat\Payment\Core\Requests;

use HcDisat\Payment\Core\Contracts\RequestContract;
use HcDisat\Payment\Core\Contracts\ResponseContract;
use HcDisat\Payment\Core\CreditCard;
use HcDisat\Payment\Core\Exceptions\InvalidRequestException;
use HcDisat\Payment\Core\ItemBag;
use HcDisat\Payment\Core\Traits\GetterAndSetterTrait;
use HcDisat\Payment\Responses\AbstractResponse;
use GuzzleHttp\Client as ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Class RequestBase
 * @package HcDisat\Payment\Core\Requests
 * @property ItemBag $items
 * @property string $testMode
 * @property string apiLiveEndpoint
 * @property string apiTestEndpoint
 * @property string apiVersion
 * @property string transactionId
 * @property CreditCard $card
 * @property string $description
 * @property string $clientIp
 * @property string $amount
 * @property string $currency
 */
abstract class RequestBase implements RequestContract
{
    use GetterAndSetterTrait { initialize as initializeTrait; }
    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var HttpRequest
     */
    protected $httpRequest;

    /**
     * @var ResponseContract
     */
    protected $response;

    /**
     * @var string
     */
    protected $httpMethod;

    /**
     * Create a new Request
     *
     * @param ClientInterface $httpClient  A Guzzle client to make API calls with
     * @param HttpRequest     $httpRequest A Symfony HTTP request object
     */
    public function __construct(ClientInterface $httpClient, HttpRequest $httpRequest)
    {
        $this->httpClient = $httpClient;
        $this->httpRequest = $httpRequest;
        $this->initialize();
    }


    /**
     * Initialize request with parameters
     * @param array $parameters The parameters to send
     * @return $this
     */
    public function initialize(array $parameters = array())
    {
        if (null !== $this->response)
            throw new \RuntimeException('Request cannot be modified after it has been sent!');

        $this->initializeTrait($parameters);

        return $this;
    }


    /**
     * Validate the request.
     *
     * This method is called internally by gateways to avoid wasting time with an API call
     * when the request is clearly invalid.
     *
     * @param array $args a variable length list of required parameters
     * @return bool
     * @throws InvalidRequestException
     */
    public function validate(...$args)
    {
        $isValid = true;
        $invalids = [];

        $argExists = function($arg){
            return !is_null($this->parameters->get($arg));
        };

        $methodExists = function($arg){
            $exists =  method_exists($this, $method = 'get'.ucfirst($arg));
            $hasValue = $exists && !is_null($this->$method());

            return $hasValue;
        };

        foreach ($args as $arg) {

            if( !$argExists($arg) || $methodExists($arg) ){
                $isValid = false;
                $invalids[] = $arg;
            }
        }

        if( !$isValid ) {
            $plural = count($invalids) > 1;
            throw new InvalidRequestException(sprintf('Parameter%s %s %s required',
                    $plural ? 's' : '',
                    implode(', ', $invalids),
                    $plural ? 'are' : 'is')
            );
        }

        return true;
    }

    /**
     * Get the response to this request (if the request has been sent)
     *
     * @return ResponseContract
     */
    public function getResponse()
    {
        if( is_null($this->response) ){
            throw new \RuntimeException('You must call send() before accessing the Response!');
        }

        return $this->response;
    }

    /**
     * Send the request
     *
     * @return ResponseContract
     */
    public function send()
    {
        return $this->sendData($this->getData());
    }
}