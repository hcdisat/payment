<?php

use HcDisat\Payment\Core\CreditCard;
use HcDisat\Payment\Core\Requests\RestCaptureRequest;
use HcDisat\Payment\Core\Requests\RestFetchPurchaseRequest;
use HcDisat\Payment\Core\Requests\RestFetchTransactionRequest;
use HcDisat\Payment\Core\RestGateway;
use HcDisat\Payment\Tests\PaymentTestCase;

class RestGatewayTest extends PaymentTestCase
{
    /** @var RestGateway */
    public $gateway;

    /** @var array */
    public $options;

    /** @var array */
    public $subscriptionOptions;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new RestGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->token = 'TEST-TOKEN-123';
        $this->gateway->tokenExpires = time() + 600;

        $this->options = [
            'amount' => '10.00',
            'card' => new CreditCard([
                'firstName' => 'Example',
                'lastName' => 'User',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2216',
                'cvv' => '123',
            ]),
        ];

        $this->subscriptionOptions = [
            'transactionReference' => 'ABC-1234',
            'description' => 'Description goes here',
        ];
    }

    public function testBearerToken()
    {
        $this->gateway->token = '';
        
        $this->setMockHttpResponse('RestTokenSuccess.txt');

        $this->assertFalse($this->gateway->hasToken());
        $this->assertEquals('A015GQlKQ6uCRzLHSGRliANi59BHw6egNVKEWRnxvTwvLr0', $this->gateway->getToken()); // triggers request
        $this->assertEquals(time() + 28800, $this->gateway->tokenExpires);
        $this->assertTrue($this->gateway->hasToken());
    }

    public function testBearerTokenReused()
    {
        $this->setMockHttpResponse('RestTokenSuccess.txt');
        $this->gateway->token = 'MYTOKEN';
        $this->gateway->tokenExpires = time() + 60;

        $this->assertTrue($this->gateway->hasToken());
        $this->assertEquals('MYTOKEN', $this->gateway->getToken());
    }

    public function testBearerTokenExpires()
    {
        $this->setMockHttpResponse('RestTokenSuccess.txt');
        $this->gateway->token = 'MYTOKEN';
        $this->gateway->tokenExpires = time() - 60;

        $this->assertFalse($this->gateway->hasToken());
        $this->assertEquals('A015GQlKQ6uCRzLHSGRliANi59BHw6egNVKEWRnxvTwvLr0', $this->gateway->getToken());
    }

    public function testAuthorize()
    {
        $this->setMockHttpResponse('RestPurchaseSuccess.txt');

        $response = $this->gateway->authorize($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('44E89981F8714392Y', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }


    public function testPurchase()
    {
        $this->setMockHttpResponse('RestPurchaseSuccess.txt');

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('44E89981F8714392Y', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testCapture()
    {
        $request = $this->gateway->capture([
            'transactionReference' => 'abc123',
            'amount' => 10.00,
            'currency' => 'AUD',
        ]);

        $this->assertInstanceOf(RestCaptureRequest::class, $request);
        $this->assertSame('abc123', $request->transactionReference);
        $endPoint = $request->getEndpoint();
        $this->assertContains($endPoint, [
            config('payment.paypal.api_rest.uri.live').'/v1/payments/authorization/abc123/capture',
            config('payment.paypal.api_rest.uri.test').'/v1/payments/authorization/abc123/capture'
        ]);
        $data = $request->getData();
        $this->assertNotEmpty($data);
    }

    public function testRefund()
    {
        $request = $this->gateway->refund([
            'transactionReference' => 'abc123',
            'amount' => 10.00,
            'currency' => 'AUD',
        ]);

        $this->assertInstanceOf(\HcDisat\Payment\Core\Requests\RestRefundRequest::class, $request);
        $this->assertSame('abc123', $request->transactionReference);
        $endPoint = $request->getEndpoint();
        $this->assertContains($endPoint, [
            config('payment.paypal.api_rest.uri.live').'/v1/payments/sale/abc123/refund',
            config('payment.paypal.api_rest.uri.test').'/v1/payments/sale/abc123/refund'
        ]);
        $data = $request->getData();
        $this->assertNotEmpty($data);
    }

    public function testFullRefund()
    {
        $request = $this->gateway->refund([
            'transactionReference' => 'abc123'
        ]);

        $this->assertInstanceOf(\HcDisat\Payment\Core\Requests\RestRefundRequest::class, $request);
        $this->assertSame('abc123', $request->transactionReference);
        $endPoint = $request->getEndpoint();

        $this->assertContains($endPoint, [
            config('payment.paypal.api_rest.uri.live').'/v1/payments/sale/abc123/refund',
            config('payment.paypal.api_rest.uri.test').'/v1/payments/sale/abc123/refund'
        ]);
        
        $data = $request->getData();

        // we're expecting an empty object here
        $json = json_encode($data);
        $this->assertEquals('{}', $json);
    }

    public function testFetchTransaction()
    {
        $request = $this->gateway->fetchTransaction(['transactionReference' => 'abc123']);

        $this->assertInstanceOf(RestFetchTransactionRequest::class, $request);
        $this->assertSame('abc123', $request->transactionReference);
        $data = $request->getData();
        $this->assertEmpty($data);
    }

    public function testFetchPurchase()
    {
        $request = $this->gateway->fetchPurchase(array('transactionReference' => 'abc123'));

        $this->assertInstanceOf(RestFetchPurchaseRequest::class, $request);
        $this->assertSame('abc123', $request->transactionReference);
        $data = $request->getData();
        $this->assertEmpty($data);
    }

    public function testCreateCard()
    {
        $this->setMockHttpResponse('RestCreateCardSuccess.txt');

        $response = $this->gateway->createCard($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('CARD-70E78145XN686604FKO3L6OQ', $response->getCardReference());
        $this->assertNull($response->getMessage());
    }

    public function testPayWithSavedCard()
    {
        $this->setMockHttpResponse('RestCreateCardSuccess.txt');
        $response = $this->gateway->createCard($this->options)->send();
        $cardRef = $response->getCardReference();

        $this->setMockHttpResponse('RestPurchaseSuccess.txt');
        $response = $this->gateway->purchase(['amount'=>'10.00', 'cardReference' => $cardRef])->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('44E89981F8714392Y', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

}