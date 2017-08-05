<?php

use HcDisat\Payment\Core\ProGateway;
use HcDisat\Payment\Core\RestGateway;
use HcDisat\Payment\Gateways\GatewayFactory;
use HcDisat\Payment\Tests\PaymentTestCase;

class GatewayFactoryTest extends PaymentTestCase
{
    /** @var GatewayFactory  */
    protected $factory;

    public function setUp()
    {
        parent::setUp();

        $this->factory = new GatewayFactory();
    }


    public function testCreate()
    {
        $pro = $this->factory->create(
            'PayPal Pro',
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $rest = $this->factory->create(
            'PayPal REST',
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $this->assertInstanceOf(ProGateway::class, $pro);
        $this->assertInstanceOf(RestGateway::class, $rest);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Foo PRO is no a valid gateway.
     */
    public function testClassNotFoundMustThrowARuntimeException()
    {
        $this->factory->create(
            'Foo PRO',
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
    }
}
