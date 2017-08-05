<?php namespace HcDisat\Payment\Core\Requests;

/**
 * Class ProAuthorizeRequest
 * @package HcDisat\Payment\Core\Requests
 * @property string $invnum
 */
class ProAuthorizeRequest extends AbstractRequest
{

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('amount', 'card');
        $this->card->validate();

        $data = $this->getBaseData();
        $data['METHOD'] = 'DoDirectPayment';
        $data['PAYMENTACTION'] = 'Authorization';
        $data['AMT'] = $this->amount;
        $data['CURRENCYCODE'] = $this->currency;
        $data['INVNUM'] = $this->invnum;
        $data['DESC'] = $this->description;

        // add credit card details
        $data['ACCT'] = $this->card->number;
        $data['CREDITCARDTYPE'] = $this->card->getBrand();
        $data['EXPDATE'] = $this->card->getExpiryDate('mY');
        $data['STARTDATE'] = $this->card->getStartDate('mY');
        $data['CVV2'] = $this->card->cvv;
        $data['ISSUENUMBER'] = $this->card->issueNumber;
        $data['IPADDRESS'] = $this->clientIp;
        $data['FIRSTNAME'] = $this->card->firstName;
        $data['LASTNAME'] = $this->card->lastName;
        $data['EMAIL'] = $this->card->email;
        $data['STREET'] = $this->card->billingAddress1;
        $data['STREET2'] = $this->card->billingAddress2;
        $data['CITY'] = $this->card->billingCity;
        $data['STATE'] = $this->card->billingState;
        $data['ZIP'] = $this->card->billingPostcode;
        $data['COUNTRYCODE'] = strtoupper($this->card->billingCountry);

        return $data;
    }
}