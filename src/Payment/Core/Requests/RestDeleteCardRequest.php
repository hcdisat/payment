<?php namespace HcDisat\Payment\Core\Requests;


class RestDeleteCardRequest extends AbstractRestRequest
{

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('cardReference');
        return [];
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return 'DELETE';
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return parent::getEndpoint().'/vault/credit-cards/'.$this->cardReference;
    }


}