<?php namespace HcDisat\Payment\Core\Requests;

/**
 * Class ReferenceTransactionRequest
 * @package HcDisat\Payment\Core\Requests
 * @property string $lname
 * @property string $lqty
 * @property string $invnum
 */
class ReferenceTransactionRequest extends AbstractRequest
{
    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('lname', 'invnum', 'transactionId');

        $data = $this->getBaseData();
        $data['METHOD'] = 'DoReferenceTransaction';
        $data['PAYMENTACTION'] = 'Sale';
        $data['AMT'] =  $data['L_AMT0'] = $data['ITEMAMT'] = $this->amount;
        $data['CURRENCYCODE'] = $this->currency;

        $data['IPADDRESS'] = $this->clientIp;
        $data['INVNUM'] = $this->invnum;
        $data['REFERENCEID'] = $this->transactionId;
        $data['DESC'] = $data['L_DESC0'] = $this->description;
        $data['L_NAME0'] = $this->lname;
        $data['L_QTY0'] = $this->lqty;

        return $data;
    }
}