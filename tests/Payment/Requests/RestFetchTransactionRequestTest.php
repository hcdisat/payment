<?php

use HcDisat\Payment\Core\Requests\RestFetchTransactionRequest;
use HcDisat\Payment\Tests\PaymentTestCase;

class RestFetchTransactionRequestTest extends PaymentTestCase
{
    /**
     * @var RestFetchTransactionRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $client = $this->getHttpClient();
        $request = $this->getHttpRequest();
        $this->request = new RestFetchTransactionRequest($client, $request);
    }

    public function testGetData()
    {
        $this->request->transactionReference = 'ABC-123';
        $data = $this->request->getData();
        $this->assertEquals(array(), $data);
    }

    public function testEndpoint()
    {
        $this->request->transactionReference = 'ABC-123';
        $this->assertStringEndsWith('/payments/sale/ABC-123', $this->request->getEndpoint());
    }
}
