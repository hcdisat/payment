<?php namespace HcDisat\Payment\Responses;


use HcDisat\Payment\Core\Contracts\RequestContract;
use HcDisat\Payment\Core\Contracts\ResponseContract;

abstract class AbstractResponse implements ResponseContract
{

    /**
     * The embodied request object.
     *
     * @var RequestContract
     */
    protected $request;

    /**
     * The data contained in the response.
     *
     * @var mixed
     */
    protected $data;

    /**
     * AbstractResponse constructor.
     * @param RequestContract $request
     * @param mixed $data
     */
    public function __construct(RequestContract $request, $data)
    {
        $this->request = $request;
        $this->data = $data;
    }


    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the original request which generated this response
     *
     * @return RequestContract
     */
    public function getRequest()
    {
        return $this->request;
    }


    /**
     * Response Message
     *
     * @return null|string A response message from the payment gateway
     */
    public function getMessage()
    {
        return null;
    }

    /**
     * Response code
     *
     * @return null|string A response code from the payment gateway
     */
    public function getCode()
    {
        return null;
    }

    /**
     * Gateway Reference
     *
     * @return null|string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        return null;
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isPending()
    {
        return false;
    }

    /**
     * Does the response require a redirect?
     *
     * @return boolean
     */
    public function isRedirect()
    {
        return false;
    }

    /**
     * Is the transaction cancelled by the user?
     *
     * @return boolean
     */
    public function isCancelled()
    {
        return false;
    }

    /**
     * Get the transaction ID as generated by the merchant website.
     *
     * @return string
     */
    public function getTransactionId()
    {
        return null;
    }
}