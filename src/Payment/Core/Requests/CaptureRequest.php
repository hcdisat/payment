<?php namespace HcDisat\Payment\Core\Requests;

class CaptureRequest extends AbstractRequest
{

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('transactionReference', 'amount');

        $data = $this->getBaseData();
        $data['METHOD'] = 'DoCapture';
        $data['AMT'] = $this->amount;
        $data['CURRENCYCODE'] = $this->currency;
        $data['AUTHORIZATIONID'] = $this->transactionReference;
        $data['COMPLETETYPE'] = 'Complete';

        return $data;
    }
}