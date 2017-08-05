<?php

use HcDisat\Payment\Core\CreditCard;
use HcDisat\Payment\Core\Requests\RestDeleteCardRequest;
use HcDisat\Payment\Tests\PaymentTestCase;

class RestDeleteCardRequestTest extends PaymentTestCase
{
    /** @var RestDeleteCardRequest */
    private $request;

    /** @var CreditCard */
    private $card;

    public function setUp()
    {
        parent::setUp();

        $this->request = new RestDeleteCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(array('cardReference' => 'CARD-TEST123'));
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertTrue(is_array($data));
        $this->assertEmpty($data);
    }

    public function testEndpoint()
    {
        $this->assertStringEndsWith('/vault/credit-cards/CARD-TEST123', $this->request->getEndpoint());
    }
}
