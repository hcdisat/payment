<?php namespace HcDisat\Payment\Core\Requests;

class RestCaptureRequest extends AbstractRestRequest
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

        return [
            'amount' => [
                'currency' => $this->currency,
                'total' => $this->amount,
            ],
            'is_final_capture' => true,
        ];
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return parent::getEndpoint() . '/payments/authorization/' . $this->transactionReference . '/capture';
    }
}