<?php

use HcDisat\Payment\Core\Requests\FetchTransactionRequest;
use HcDisat\Payment\Tests\PaymentTestCase;

class FetchTransactionRequestTest extends PaymentTestCase
{
    /**
     * @var FetchTransactionRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $client = $this->getHttpClient();

        $request = $this->getHttpRequest();

        $this->request = new FetchTransactionRequest($client, $request);
    }

    public function testGetData()
    {
        $this->request->transactionReference = 'ABC-123';
        $this->request->username = 'testuser';
        $this->request->password = 'testpass';
        $this->request->signature = 'SIG';
        $this->request->subject = 'SUB';

        $expected = array();
        $expected['METHOD'] = 'GetTransactionDetails';
        $expected['TRANSACTIONID'] = 'ABC-123';
        $expected['USER'] = 'testuser';
        $expected['PWD'] = 'testpass';
        $expected['SIGNATURE'] = 'SIG';
        $expected['SUBJECT'] = 'SUB';
        $expected['VERSION'] = 119.0;

        $this->assertEquals($expected, $this->request->getData());
    }
}
