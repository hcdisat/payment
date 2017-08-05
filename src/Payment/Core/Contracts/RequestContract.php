<?php namespace HcDisat\Payment\Core\Contracts;

interface RequestContract
{
    /**
     * Initialize request with parameters
     * @param array $parameters The parameters to send
     */
    public function initialize(array $parameters = array());

    /**
     * Get all request parameters
     *
     * @return array
     */
    public function getParameters();


    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData();

    /**
     * Get the response to this request (if the request has been sent)
     *
     * @return ResponseContract
     */
    public function getResponse();

    /**
     * Send the request
     *
     * @return ResponseContract
     */
    public function send();

    /**
     * Send the request with specified data
     *
     * @param  mixed             $data The data to send
     * @return ResponseContract
     */
    public function sendData($data);
}