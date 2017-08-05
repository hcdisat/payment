<?php

use HcDisat\Payment\Core\CreditCard;
use HcDisat\Payment\Core\Requests\ProPurchaseRequest;
use HcDisat\Payment\Tests\PaymentTestCase;

class ProPurchaseRequestTest extends PaymentTestCase
{
    /**
     * @var ProPurchaseRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $this->request = new ProPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => '10.00',
                'currency' => 'USD',
                'card' => $this->getValidCard(),
            )
        );
    }

    public function testGetData()
    {
        $card = new CreditCard($this->getValidCard());
        $card->startMonth = 1;
        $card->startYear = 2000;

        $this->request->card = $card;
        $this->request->invnum = 'abc123';
        $this->request->description = 'Sheep';
        $this->request->clientIp = '127.0.0.1';

        $data = $this->request->getData();

        $this->assertSame('DoDirectPayment', $data['METHOD']);
        $this->assertSame('Sale', $data['PAYMENTACTION']);
        $this->assertSame('10.00', $data['AMT']);
        $this->assertSame('USD', $data['CURRENCYCODE']);
        $this->assertSame('abc123', $data['INVNUM']);
        $this->assertSame('Sheep', $data['DESC']);
        $this->assertSame('127.0.0.1', $data['IPADDRESS']);

        $this->assertSame($card->number, $data['ACCT']);
        $this->assertSame($card->getBrand(), $data['CREDITCARDTYPE']);
        $this->assertEquals($card->getExpiryDate('mY'), $data['EXPDATE']);
        $this->assertSame('012000', $data['STARTDATE']);
        $this->assertSame($card->cvv, $data['CVV2']);
        $this->assertSame($card->issueNumber, $data['ISSUENUMBER']);

        $this->assertSame($card->firstName, $data['FIRSTNAME']);
        $this->assertSame($card->lastName, $data['LASTNAME']);
        $this->assertSame($card->email, $data['EMAIL']);
        $this->assertSame($card->billingAddress1, $data['STREET']);
        $this->assertSame($card->billingAddress2, $data['STREET2']);
        $this->assertSame($card->billingCity, $data['CITY']);
        $this->assertSame($card->billingState, $data['STATE']);
        $this->assertSame($card->billingPostcode, $data['ZIP']);
        $this->assertSame($card->billingCountry, $data['COUNTRYCODE']);
    }
}
