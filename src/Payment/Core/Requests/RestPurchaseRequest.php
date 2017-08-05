<?php namespace HcDisat\Payment\Core\Requests;

use HcDisat\Payment\Core\Contracts\RequestContract;

class RestPurchaseRequest extends RestAuthorizeRequest implements RequestContract
{

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $data = parent::getData();
        $data['intent'] = 'sale';
        return $data;
    }
}