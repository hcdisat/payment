<?php

use HcDisat\Payment\Core\Requests\RestFetchPurchaseRequest;
use HcDisat\Payment\Tests\PaymentTestCase;

class RestFetchPurchaseRequestTest extends PaymentTestCase
{
    /**
     * @var \HcDisat\Payment\Core\Requests\RestFetchPurchaseRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();
        
        $client = $this->getHttpClient();
        $request = $this->getHttpRequest();
        $this->request = new RestFetchPurchaseRequest($client, $request);
    }

    public function testEndpoint()
    {
        $this->request->transactionReference = 'ABC-123';
        $this->assertStringEndsWith('/payments/payment/ABC-123', $this->request->getEndpoint());
    }
}
