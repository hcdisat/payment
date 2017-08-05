<?php namespace HcDisat\Payment\Requests;

use HcDisat\Payment\Core\Requests\RefundRequest;
use HcDisat\Payment\Tests\PaymentTestCase;

class RefundRequestTest extends PaymentTestCase
{
    /**
     * @var RefundRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();
        
        $client = $this->getHttpClient();

        $request = $this->getHttpRequest();

        $this->request = new RefundRequest($client, $request);
    }

    /**
     * @dataProvider provideRefundTypes
     * @param $type
     * @param $amount
     */
    public function testGetData($type, $amount)
    {
        $this->request->transactionReference = 'ABC-123';
        $this->request->amount = $amount;
        $this->request->currency = 'USD';
        $this->request->username = 'testuser';
        $this->request->password = 'testpass';
        $this->request->signature = 'SIG';
        $this->request->subject = 'SUB';

        $expected = array();
        $expected['REFUNDTYPE'] = $type;
        $expected['METHOD'] = 'RefundTransaction';
        $expected['TRANSACTIONID'] = 'ABC-123';
        $expected['USER'] = 'testuser';
        $expected['PWD'] = 'testpass';
        $expected['SIGNATURE'] = 'SIG';
        $expected['SUBJECT'] = 'SUB';
        $expected['VERSION'] = 119.0;
        // $amount will be a formatted string, and '0.00' evaluates to true
        if ((float)$amount) {
            $expected['AMT'] = $amount;
            $expected['CURRENCYCODE'] = 'USD';
        }

        $this->assertEquals($expected, $this->request->getData());
    }

    public function provideRefundTypes()
    {
        return array(
            'Partial' => array('Partial', '1.23'),
            // All amounts must include decimals or be a float if the currency supports decimals.
            'Full' => array('Full', '0.00'),
        );
    }
}
