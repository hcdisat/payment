<?php namespace HcDisat\Payment\Core\Requests;

class RefundRequest extends AbstractRequest
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
        $data['METHOD'] = 'RefundTransaction';
        $data['TRANSACTIONID'] = $this->transactionReference;
        $data['REFUNDTYPE'] = 'Full';
        if ($this->amount > 0) {
            $data['REFUNDTYPE'] = 'Partial';
            $data['AMT'] = $this->amount;
            $data['CURRENCYCODE'] = $this->currency;
        }

        return $data;
    }
}