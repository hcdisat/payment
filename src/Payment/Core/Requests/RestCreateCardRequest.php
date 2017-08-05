<?php namespace HcDisat\Payment\Core\Requests;


class RestCreateCardRequest extends AbstractRestRequest
{

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('card');
        $this->card->validate();

        return [
            'number' => $this->card->number,
            'type' => $this->card->getBrand(),
            'expire_month' => $this->card->expiryMonth,
            'expire_year' => $this->card->expiryYear,
            'cvv2' => $this->card->cvv,
            'first_name' => $this->card->firstName,
            'last_name' => $this->card->lastName,
            'billing_address' => [
                'line1' => $this->card->billingAddress1,
                'line2' => $this->card->billingAddress2 ?? null,
                'city' => $this->card->billingCity,
                'state' => $this->card->billingState,
                'postal_code' => $this->card->billingPostcode,
                'country_code' => strtoupper($this->card->billingCountry),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getEndpoint()
    {
        return parent::getEndpoint() . '/vault/credit-cards';
    }
}