<?php

use HcDisat\Payment\Core\CreditCard;
use HcDisat\Payment\Core\ProGateway;
use HcDisat\Payment\Core\Requests\FetchTransactionRequest;
use HcDisat\Payment\Tests\PaymentTestCase;

class ProGatewayTest extends PaymentTestCase
{
    /** @var  ProGateway  */
    protected $gateway;

    /** @var  array */
    protected $options;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new ProGateway($this->getHttpClient(), $this->getHttpRequest());

        $this->options = [
            'amount' => '10.00',
            'card' => new CreditCard([
                'firstName' => 'Example',
                'lastName' => 'User',
                'number' => '4032031662872158',
                'expiryMonth' => '12',
                'expiryYear' => '2776',
                'cvv' => '123',
            ]),
        ];
    }

    public function testAuthorize()
    {
        $this->setMockHttpResponse('ProPurchaseSuccess.txt');

        $response = $this->gateway->authorize($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('96U93778BD657313D', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testPurchase()
    {
        $this->setMockHttpResponse('ProPurchaseSuccess.txt');

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('96U93778BD657313D', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testFetchTransaction()
    {
        $request = $this->gateway->fetchTransaction(array('transactionReference' => 'abc123'));

        $this->assertInstanceOf(FetchTransactionRequest::class, $request);
        $this->assertSame('abc123', $request->transactionReference);
    }
}
