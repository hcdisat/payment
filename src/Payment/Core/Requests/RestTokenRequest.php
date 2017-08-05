<?php namespace HcDisat\Payment\Core\Requests;

use HcDisat\Payment\Core\Contracts\RequestContract;
use HcDisat\Payment\Responses\RestResponse;

class RestTokenRequest extends AbstractRestRequest implements RequestContract
{

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return RestResponse
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

        $httpRequest = $this->httpClient->createRequest(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            $this->setOptions($data)
        );

        $httpResponse = $this->httpClient->send($httpRequest);
        return $this->response = new RestResponse(
            $this, $httpResponse->json(),
            $httpResponse->getStatusCode()
        );
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        return ['grant_type' => 'client_credentials'];
    }

    /**
     * @return string
     */
    protected function getEndpoint()
    {
        return parent::getEndpoint() . '/oauth2/token';
    }
}