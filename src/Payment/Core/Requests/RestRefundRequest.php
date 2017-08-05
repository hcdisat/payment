<?php namespace HcDisat\Payment\Core\Requests;

class RestRefundRequest extends AbstractRestRequest
{

    public function getData()
    {
        $this->validate('transactionReference');

        if ($this->amount > 0) {
            return array(
                'amount' => array(
                    'currency' => $this->currency,
                    'total' => $this->amount,
                ),
                'description' => $this->description,
            );
        } else {
            return new \stdClass();
        }
    }

    public function getEndpoint()
    {
        return parent::getEndpoint() . '/payments/sale/' . $this->transactionReference . '/refund';
    }
}