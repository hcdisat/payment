<?php namespace HcDisat\Payment\Core\Contracts;

interface GatewayContract
{
    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     */
    public function getName();
    
    /**
     * Define gateway parameters, in the following format:
     *
     * array(
     *     'username' => '', // string variable
     *     'testMode' => false, // boolean variable
     *     'landingPage' => array('billing', 'login'), // enum variable, first item is default
     * );
     */
    public function getDefaultParameters();

    /**
     * Initialize gateway with parameters
     * @param array $parameters
     * @return
     */
    public function initialize(array $parameters = array());

    /**
     * Get all gateway parameters
     *
     * @return array
     */
    public function getParameters();
}