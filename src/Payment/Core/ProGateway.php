<?php namespace HcDisat\Payment\Core;

use HcDisat\Payment\Core\Requests\CaptureRequest;
use HcDisat\Payment\Core\Requests\FetchTransactionRequest;
use HcDisat\Payment\Core\Requests\ProAuthorizeRequest;
use HcDisat\Payment\Core\Requests\ProPurchaseRequest;
use HcDisat\Payment\Core\Requests\ReferenceTransactionRequest;
use HcDisat\Payment\Core\Requests\RefundRequest;
use HcDisat\Payment\Gateways\AbstractGateway;

/**
 * Class ProGateway
 * @package HcDisat\Payment\Core
 *
 * @property string $username
 * @property string $password
 * @property string $signature
 */
class ProGateway extends AbstractGateway
{
    /**
     * Define gateway parameters, in the following format:
     *
     * array(
     *     'username' => '', // string variable
     *     'testMode' => false, // boolean variable
     *     'landingPage' => array('billing', 'login'), // enum variable, first item is default
     * );
     */
    public function getDefaultParameters()
    {
        return config('payment.paypal.nvp.credentials');
    }

    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     */
    public function getName()
    {
        return config('payment.gateways.paypal_pro.name');
    }

    /**
     * Create an authorization request.
     *
     * To collect payment at a later time, first authorize a payment using the /payment resource.
     * You can then capture the payment to complete the sale and collect payment.
     *
     * @link https://developer.paypal.com/docs/integration/direct/capture-payment/#authorize-the-payment
     * @link https://developer.paypal.com/docs/api/#authorizations
     * @param array $parameters
     * @return ProAuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest(ProAuthorizeRequest::class, $parameters);
    }

    /**
     * Create a purchase request.
     *
     * PayPal provides various payment related operations using the /payment
     * resource and related sub-resources. Use payment for direct credit card
     * payments and PayPal account payments. You can also use sub-resources
     * to get payment related details.
     *
     * @link https://developer.paypal.com/docs/api/#create-a-payment
     * @param array $parameters
     * @return ProPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest(ProPurchaseRequest::class, $parameters);
    }

    /**
     * Capture an authorization.
     *
     * Use this resource to capture and process a previously created authorization.
     * To use this resource, the original payment call must have the intent set to
     * authorize.
     *
     * @link https://developer.paypal.com/docs/api/#capture-an-authorization
     * @param array $parameters
     * @return CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest(CaptureRequest::class, $parameters);
    }

    /**
     * Refund a Sale Transaction
     *
     * To get details about completed payments (sale transaction) created by a payment request
     * or to refund a direct sale transaction, PayPal provides the /sale resource and related
     * sub-resources.
     *
     * @link https://developer.paypal.com/docs/api/#sale-transactions
     * @param array $parameters
     * @return RefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest(RefundRequest::class, $parameters);
    }

    /**
     * Fetch a Sale Transaction
     *
     * To get details about completed payments (sale transaction) created by a payment request
     * or to refund a direct sale transaction, PayPal provides the /sale resource and related
     * sub-resources.
     *
     * @link https://developer.paypal.com/docs/api/#sale-transactions
     * @param array $parameters
     * @return FetchTransactionRequest
     */
    public function fetchTransaction(array $parameters = array())
    {
        return $this->createRequest(FetchTransactionRequest::class, $parameters);
    }

    /**
     * @param array $parameters
     * @return ReferenceTransactionRequest
     *
     */
    public function referenceTransaction(array $parameters = array())
    {
        return $this->createRequest(ReferenceTransactionRequest::class, $parameters);
    }
}