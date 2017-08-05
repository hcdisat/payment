<?php

use HcDisat\Payment\Core\Responses\RestAuthorizeResponse;
use HcDisat\Payment\Tests\PaymentTestCase;

class RestAuthorizeResponseTest extends PaymentTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testRestPurchaseWithoutCardSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('RestPurchaseWithoutCardSuccess.txt');
        $response = new RestAuthorizeResponse($this->getMockRequest(), (array)$httpResponse->json(), $httpResponse->getStatusCode());

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('PAY-3TJ47806DA028052TKTQGVYI', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
        
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertSame('https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-5KV58254GL528393N', $response->getRedirectUrl());
    }
}
