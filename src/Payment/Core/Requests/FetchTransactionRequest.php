<?php namespace HcDisat\Payment\Core\Requests;


class FetchTransactionRequest extends AbstractRequest
{

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('transactionReference');

        $data = $this->getBaseData();
        $data['METHOD'] = 'GetTransactionDetails';
        $data['TRANSACTIONID'] = $this->transactionReference;

        return $data;
    }
}