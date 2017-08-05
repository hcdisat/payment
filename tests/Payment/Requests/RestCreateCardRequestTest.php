<?php

use HcDisat\Payment\Core\CreditCard;
use HcDisat\Payment\Core\Requests\RestCreateCardRequest;
use HcDisat\Payment\Tests\PaymentTestCase;

class RestCreateCardRequestTest extends PaymentTestCase
{
    /** @var RestCreateCardRequest*/
    protected $request;

    /** @var CreditCard */
    protected $card;

    public function setUp()
    {
        parent::setUp();

        $this->request = new RestCreateCardRequest($this->getHttpClient(), $this->getHttpRequest());

        $card = $this->getValidCard();
        $this->card = new CreditCard($card);

        $this->request->initialize(['card' => $this->card]);
    }

    public function testGetData()
    {
        $card = $this->card;
        $data = $this->request->getData();

        $this->assertSame($card->number, $data['number']);
        $this->assertSame($card->getBrand(), $data['type']);
        $this->assertSame($card->expiryMonth, $data['expire_month']);
        $this->assertSame($card->expiryYear, $data['expire_year']);
        $this->assertSame($card->cvv, $data['cvv2']);
        $this->assertSame($card->firstName, $data['first_name']);
        $this->assertSame($card->lastName, $data['last_name']);
        $this->assertSame($card->billingAddress1, $data['billing_address']['line1']);
        $this->assertSame($card->billingAddress2, $data['billing_address']['line2']);
        $this->assertSame($card->billingCity, $data['billing_address']['city']);
        $this->assertSame($card->billingState, $data['billing_address']['state']);
        $this->assertSame($card->billingPostcode, $data['billing_address']['postal_code']);
        $this->assertSame($card->billingCountry, $data['billing_address']['country_code']);
    }
}
