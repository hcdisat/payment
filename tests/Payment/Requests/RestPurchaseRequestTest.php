<?php

use HcDisat\Payment\Core\CreditCard;
use HcDisat\Payment\Core\Requests\RestPurchaseRequest;
use HcDisat\Payment\Tests\PaymentTestCase;

class RestPurchaseRequestTest extends PaymentTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @var RestPurchaseRequest */
    private $request;

    public function testGetData()
    {
        $card = new CreditCard($this->getValidCard());
        $card->startMonth = 1;
        $card->startYear ='2000';

        $this->request = new RestPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(array(
            'amount' => '10.00',
            'currency' => 'USD',
            'card' => $card
        ));

        $this->request->transactionId = 'abc123';
        $this->request->description = 'Sheep';
        $this->request->clientIp ='127.0.0.1';

        $data = $this->request->getData();

        $this->assertSame('sale', $data['intent']);
        $this->assertSame('credit_card', $data['payer']['payment_method']);
        $this->assertSame('10.00', $data['transactions'][0]['amount']['total']);
        $this->assertSame('USD', $data['transactions'][0]['amount']['currency']);
        $this->assertSame('abc123 : Sheep', $data['transactions'][0]['description']);

        $this->assertSame($card->number, $data['payer']['funding_instruments'][0]['credit_card']['number']);
        $this->assertSame($card->getBrand(), $data['payer']['funding_instruments'][0]['credit_card']['type']);
        $this->assertSame($card->expiryMonth, $data['payer']['funding_instruments'][0]['credit_card']['expire_month']);
        $this->assertSame($card->expiryYear, $data['payer']['funding_instruments'][0]['credit_card']['expire_year']);
        $this->assertSame($card->cvv, $data['payer']['funding_instruments'][0]['credit_card']['cvv2']);

        $this->assertSame($card->firstName, $data['payer']['funding_instruments'][0]['credit_card']['first_name']);
        $this->assertSame($card->lastName, $data['payer']['funding_instruments'][0]['credit_card']['last_name']);
        $this->assertSame($card->billingAddress1, $data['payer']['funding_instruments'][0]['credit_card']['billing_address']['line1']);
        $this->assertSame($card->billingAddress2, $data['payer']['funding_instruments'][0]['credit_card']['billing_address']['line2']);
        $this->assertSame($card->billingCity, $data['payer']['funding_instruments'][0]['credit_card']['billing_address']['city']);
        $this->assertSame($card->billingState, $data['payer']['funding_instruments'][0]['credit_card']['billing_address']['state']);
        $this->assertSame($card->billingPostcode, $data['payer']['funding_instruments'][0]['credit_card']['billing_address']['postal_code']);
        $this->assertSame($card->billingCountry, $data['payer']['funding_instruments'][0]['credit_card']['billing_address']['country_code']);
    }

    public function testGetDataWithCardRef()
    {
        $this->request = new RestPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(array(
            'amount' => '10.00',
            'currency' => 'USD',
            'cardReference' => 'CARD-123',
        ));

        $this->request->transactionId = 'abc123';
        $this->request->description = 'Sheep';
        $this->request->clientIp = '127.0.0.1';

        $data = $this->request->getData();

        $this->assertSame('sale', $data['intent']);
        $this->assertSame('credit_card', $data['payer']['payment_method']);
        $this->assertSame('10.00', $data['transactions'][0]['amount']['total']);
        $this->assertSame('USD', $data['transactions'][0]['amount']['currency']);
        $this->assertSame('abc123 : Sheep', $data['transactions'][0]['description']);
        $this->assertSame('CARD-123', $data['payer']['funding_instruments'][0]['credit_card_token']['credit_card_id']);
    }
}
