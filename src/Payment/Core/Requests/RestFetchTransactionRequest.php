<?php namespace HcDisat\Payment\Core\Requests;

/**
 * Class RestFetchTransactionRequest
 * @package HcDisat\Payment\Core\Requests
 */
class RestFetchTransactionRequest extends RestAuthorizeRequest
{
    /**
     * @inheritdoc
     */
    public function getData()
    {
        $this->validate('transactionReference');
        return [];
    }

    /**
     * Get HTTP Method.
     *
     * The HTTP method for fetchTransaction requests must be GET.
     * Using POST results in an error 500 from PayPal.
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return 'GET';
    }


    /**
     * @inheritdoc
     */
    public function getEndpoint()
    {
        return parent::getEndpoint().'/payments/sale/'.$this->transactionReference;
    }


}