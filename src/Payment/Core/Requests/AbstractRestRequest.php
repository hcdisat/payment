<?php namespace HcDisat\Payment\Core\Requests;

use HcDisat\Payment\Core\Contracts\ResponseContract;
use HcDisat\Payment\Core\Exceptions\InvalidResponseException;
use HcDisat\Payment\Responses\RestResponse;
use Illuminate\Support\Collection;

/**
 * Class RestAuthorizeRequest
 * @package HcDisat\Payment\Core\Requests
 *
 * @property string $clientId
 * @property string $secret
 * @property string $token
 * @property string $payerId$
 * @property Collection $parameters
 * @property array $data
 * @property string $cardReference
 * @property string $amount
 * @property string $amountInteger 
 * @property string $currencyNumeric
 * @property string $currencyDecimalPlaces
 * @property string $transactionId
 * @property string $transactionReference
 * @property string $returnUrl
 * @property string $cancelUrl
 * @property string $notifyUrl
 * @property string $issuer
 * @property string $paymentMethod
 * @property ResponseContract $response 
 */
abstract class AbstractRestRequest extends RequestBase
{

    public function initialize(array $parameters = [])
    {
        $api = [
            'apiLiveEndpoint' => config('payment.paypal.api_rest.uri.live'),
            'apiTestEndpoint' => config('payment.paypal.api_rest.uri.test'),
            'apiVersion' => config('payment.paypal.api_rest.version'),
            'secret' => config('payment.paypal.api_rest.credentials.secret'),
            'clientId' => config('payment.paypal.api_rest.credentials.clientId'),
            'testMode' => config('payment.paypal.api_rest.credentials.testMode')
        ];

        foreach ($api as $key => $value) {
            $parameters[$key] = $value;
        }

        return parent::initialize($parameters);
    }

    /**
     * this will be post must of times.
     * @return string
     */
    protected function getHttpMethod()
    {
        return 'POST';
    }
    
    

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseContract
     * @throws InvalidResponseException
     */
    public function sendData($data)
    {
        // don't throw exceptions for 4xx errors
        $this->httpClient->getEmitter()->on(
            'request.error',
            function ($event) {
                if ($event['response']->isClientError()) {
                    $event->stopPropagation();
                }
            }
        );

        // Guzzle HTTP Client createRequest does funny things when a GET request
        // has attached data, so don't send the data if the method is GET.
        $httpRequest = $this->httpClient->createRequest(
            $this->getHttpMethod(),
            $this->getEndpoint() . '?' . http_build_query($data),
            $this->setOptions($data)
        );

        // Might be useful to have some debug code here, PayPal especially can be
        // a bit fussy about data formats and ordering.  Perhaps hook to whatever
        // logging engine is being used.
        // echo "Data == " . json_encode($data) . "\n";

        try {
            $httpResponse = $this->httpClient->send($httpRequest);
            return $this->response = $this->createResponse($httpResponse->json(), $httpResponse->getStatusCode());
        } catch (\Exception $e) {
            throw new InvalidResponseException(
                'Error communicating with payment gateway: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }

    /**
     * get the api uri
     * @return string
     */
    protected function getEndpoint()
    {
        $uri = $this->testMode
            ? $this->apiTestEndpoint
            : $this->apiLiveEndpoint;

      return $uri.'/'.$this->apiVersion;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param $data
     * @param  int $options
     * @return string
     */
    public function toJson($data, $options = 0)
    {
        if( $data instanceof Collection)
            return $data->toJson();
        if( is_array($data ) )
            return json_encode($data, $options | 64);
    }

    /**
     * @param $data
     * @param $statusCode
     * @return RestResponse
     */
    protected function createResponse($data, $statusCode)
    {
        return $this->response = new RestResponse($this, $data, $statusCode);
    }

    /**
     * @param array $options
     * @return array
     */
    protected function setOptions(array $options)
    {
        return array_merge([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
                'username' => $this->clientId,
                'password' => $this->secret
            ],
            $options
        ]);
    }
}