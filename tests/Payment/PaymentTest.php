<?php
use Tests\TestCase;

class PaymentTest extends TestCase
{

    public function testGetFactory()
    {
        $factory = \HcDisat\Payment\Payment::getFactory();

        $this->assertInstanceOf(\HcDisat\Payment\Gateways\GatewayFactory::class, $factory);
    }

    public function testSetFactory()
    {
        $factory = Mockery::mock(\HcDisat\Payment\Gateways\GatewayFactory::class);

        \HcDisat\Payment\Payment::setFactory($factory);

        $this->assertSame($factory, \HcDisat\Payment\Payment::getFactory());
    }

    public function testCallStatic()
    {
        $factory = Mockery::mock(\HcDisat\Payment\Gateways\GatewayFactory::class);
        $factory->shouldReceive('testMethod')
            ->with('some-argument')
            ->once()
            ->andReturn('some-result');

        \HcDisat\Payment\Payment::setFactory($factory);

        $this->assertEquals('some-result', \HcDisat\Payment\Payment::testMethod('some-argument'));
    }
}
