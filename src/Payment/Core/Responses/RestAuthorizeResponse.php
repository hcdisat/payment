<?php namespace HcDisat\Payment\Core\Responses;

use HcDisat\Payment\Responses\RestResponse;

class RestAuthorizeResponse extends RestResponse
{
    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return empty($this->data['error']) && $this->getCode() == 201;
    }

    
    /**
     * Get the URL to complete (execute) the purchase or agreement.
     *
     * The URL is embedded in the links section of the purchase or create
     * subscription request response.
     *
     * @return string
     */
    public function getCompleteUrl()
    {
        if ( isset($this->data['links']) && is_array($this->data['links']) ) {
            foreach ($this->data['links'] as $key => $value) {
                if ( $value['rel'] == 'execute' ) {
                    return $value['href'];
                }
            }
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function getTransactionReference()
    {
        // The transaction reference for a paypal purchase request or for a
        // paypal create subscription request ends up in the execute URL
        // in the links section of the response.
        $completeUrl = $this->getCompleteUrl();
        if ( empty($completeUrl) ) {
            return parent::getTransactionReference();
        }

        $urlParts = explode('/', $completeUrl);

        // The last element of the URL should be "execute"
        $execute = end($urlParts);
        if ( $execute != 'execute' ) {
            return parent::getTransactionReference();
        }

        // The penultimate element should be the transaction reference
        return prev($urlParts);
    }

    /**
     * Get the required redirect method (either GET or POST).
     *
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }
}