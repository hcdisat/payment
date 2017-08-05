<?php

use HcDisat\Payment\Core\Requests\CaptureRequest;
use HcDisat\Payment\Tests\PaymentTestCase;

class CaptureRequestTest extends PaymentTestCase
{
    /**
     * @var CaptureRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();
        $client = $this->getHttpClient();
        $request = $this->getHttpRequest();
        $this->request = new CaptureRequest($client, $request);
    }

    public function testGetData()
    {
        $this->request->transactionReference = 'ABC-123';
        $this->request->amount = '1.23';
        $this->request->currency = 'USD';
        $this->request->username = 'testuser';
        $this->request->password = 'testpass';
        $this->request->signature = 'SIG';
        $this->request->subject = 'SUB';
        $this->request->buttonSource = 'BNCode_PP';

        $expected = [];
        $expected['METHOD'] = 'DoCapture';
        $expected['AUTHORIZATIONID'] = 'ABC-123';
        $expected['AMT'] = '1.23';
        $expected['CURRENCYCODE'] = 'USD';
        $expected['COMPLETETYPE'] = 'Complete';
        $expected['USER'] = 'testuser';
        $expected['PWD'] = 'testpass';
        $expected['SIGNATURE'] = 'SIG';
        $expected['SUBJECT'] = 'SUB';
        $expected['BUTTONSOURCE'] = 'BNCode_PP';
        $expected['VERSION'] = 119.0;

        $this->assertEquals($expected, $this->request->getData());
    }
}
