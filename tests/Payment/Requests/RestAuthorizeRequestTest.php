<?php

use HcDisat\Payment\Core\CreditCard;
use HcDisat\Payment\Core\Requests\AbstractRestRequest;
use HcDisat\Payment\Core\Requests\RestAuthorizeRequest;
use HcDisat\Payment\Tests\PaymentTestCase;

class RestAuthorizeRequestTest extends PaymentTestCase
{

    /**
     * @var RestAuthorizeRequest
     */
    protected $restRequest;

    public function setUp()
    {
        parent::setUp();
        $this->restRequest = new RestAuthorizeRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $this->restRequest->initialize([
            'amount' => '10.00',
            'currency' => 'USD',
            'returnUrl' => 'https://www.example.com/return',
            'cancelUrl' => 'https://www.example.com/cancel',
        ]);
    }

    public function testInitializeBeforeSendRequest()
    {
        $this->assertInstanceOf(
            AbstractRestRequest::class,
            $this->restRequest->initialize()
        );
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage You must call send() before accessing the Response!
     */
    public function testGetResponseBeforeSendRequest()
    {
        $this->restRequest->getResponse();
    }

    /**
     * @expectedException \HcDisat\Payment\Core\Exceptions\InvalidRequestException
     * @expectedExceptionMessage Parameter billableId is required
     */
    public function testValidateFails()
    {
        $this->restRequest->initialize();

        $this->restRequest->validate('billableId');
    }

    /**
     * @expectedException \HcDisat\Payment\Core\Exceptions\InvalidRequestException
     * @expectedExceptionMessage Parameter billableId is required
     */
    public function testValidateFailsIfArgumentIsNull()
    {
        $this->restRequest->initialize([
            'billable_id' => null
        ]);

        $this->restRequest->validate('billableId');
    }

    public function testValidationPass()
    {
        $this->restRequest->initialize([
            'billable_id' => 66
        ]);

        $this->assertTrue($this->restRequest->validate('billableId'));
    }

    public function testAmounts()
    {
        $value = '99';
        $this->restRequest->amount = $value;

        $this->assertEquals($value, $this->restRequest->amount);
    }

    public function testGetApiDetails()
    {
        $restRequest = new RestAuthorizeRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $this->assertEquals(
            config('payment.paypal.api_rest.uri.live'),
            $restRequest->apiLiveEndpoint
        );
        $this->assertEquals(
            config('payment.paypal.api_rest.uri.test'),
            $restRequest->apiTestEndpoint
        );
        $this->assertEquals(
            config('payment.paypal.api_rest.version'),
            $restRequest->apiVersion
        );

    }

    public function testGetDataWithOutCard()
    {
        $this->restRequest->transactionId = 'abc123';
        $this->restRequest->description = 'Sheep';

        $data = $this->restRequest->getData();

        $this->assertSame('authorize', $data['intent']);
        $this->assertSame('paypal', $data['payer']['payment_method']);
        $this->assertSame('10.00', $data['transactions'][0]['amount']['total']);
        $this->assertSame('USD', $data['transactions'][0]['amount']['currency']);
        $this->assertSame('abc123 : Sheep', $data['transactions'][0]['description']);

        // Funding instruments must not be set, otherwise paypal API will give error 500.
        $this->assertArrayNotHasKey('funding_instruments', $data['payer']);

        $this->assertSame('https://www.example.com/return', $data['redirect_urls']['return_url']);
        $this->assertSame('https://www.example.com/cancel', $data['redirect_urls']['cancel_url']);
    }

    public function testGetDataWithCard()
    {
        $card = new CreditCard($this->getValidCard());
        $card->startMonth = 1;
        $card->startYear = 2000;

        $this->restRequest->card = $card;
        $this->restRequest->transactionId = 'abc123';
        $this->restRequest->description = 'Sheep';
        $this->restRequest->clientIp = '127.0.0.1';

        $data = $this->restRequest->getData();

        $this->assertSame('authorize', $data['intent']);
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

    public function testGetDataWithItems()
    {
        $this->restRequest->amount = '50.00';
        $this->restRequest->currency = 'USD';
        $this->restRequest->items = ([
            ['name' => 'Floppy Disk', 'description' => 'MS-DOS', 'quantity' => 2, 'price' => 10*100],
            ['name' => 'CD-ROM', 'description' => 'Windows 95', 'quantity' => 1, 'price' => 40*100],
        ]);

        $data = $this->restRequest->getData();
        $transactionData = $data['transactions'][0];

        $this->assertSame('Floppy Disk', $transactionData['item_list']['items'][0]['name']);
        $this->assertSame('MS-DOS', $transactionData['item_list']['items'][0]['description']);
        $this->assertSame(2, $transactionData['item_list']['items'][0]['quantity']);
        $this->assertSame('10.00', $transactionData['item_list']['items'][0]['price']);

        $this->assertSame('CD-ROM', $transactionData['item_list']['items'][1]['name']);
        $this->assertSame('Windows 95', $transactionData['item_list']['items'][1]['description']);
        $this->assertSame(1, $transactionData['item_list']['items'][1]['quantity']);
        $this->assertSame('40.00', $transactionData['item_list']['items'][1]['price']);

        $this->assertSame('50.00', $transactionData['amount']['total']);
        $this->assertSame('USD', $transactionData['amount']['currency']);
    }

    public function testDescription()
    {
        $this->restRequest->transactionId = '';
        $this->restRequest->description = '';
        $this->assertEmpty($this->restRequest->getDescription());

        $this->restRequest->transactionId = '';
        $this->restRequest->description = 'Sheep';
        $this->assertEquals('Sheep', $this->restRequest->getDescription());

        $this->restRequest->transactionId = 'abc123';
        $this->restRequest->description = '';
        $this->assertEquals('abc123', $this->restRequest->getDescription());

        $this->restRequest->transactionId = 'abc123';
        $this->restRequest->description = 'Sheep';
        $this->assertEquals('abc123 : Sheep', $this->restRequest->getDescription());
    }

}
