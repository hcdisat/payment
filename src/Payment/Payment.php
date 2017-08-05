<?php namespace HcDisat\Payment;

use HcDisat\Payment\Core\RestGateway;
use HcDisat\Payment\Gateways\GatewayFactory;

class Payment
{
    /**
     * @var GatewayFactory
     */
    private static $_factory;
    /**

     * Create a Gateway instance
     * @param $method
     * @param $parameters
     * @return RestGateway
     */
    public static function __callStatic($method, $parameters)
    {
        $factory = self::getFactory();
        return call_user_func_array([$factory, $method], $parameters);
    }

    /**
     * @return GatewayFactory
     */
    public static function getFactory()
    {
        self::$_factory = self::$_factory ?? new GatewayFactory();
        return self::$_factory;
    }

    /**
     * @param GatewayFactory $factory
     */
    public static function setFactory(GatewayFactory $factory = null)
    {
        self::$_factory = $factory;
    }
}