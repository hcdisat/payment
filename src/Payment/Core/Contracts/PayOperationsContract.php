<?php namespace HcDisat\Payment\Core;

use HcDisat\Payment\Core\Requests\ReferenceTransactionRequest;
use HcDisat\Payment\Core\Requests\RestAuthorizeRequest;
use HcDisat\Payment\Core\Requests\RestCaptureRequest;
use HcDisat\Payment\Core\Requests\RestFetchTransactionRequest;
use HcDisat\Payment\Core\Requests\RestPurchaseRequest;
use HcDisat\Payment\Core\Requests\RestRefundRequest;


/**
 * Class Gateway
 * @package HcDisat\Payment\Core
 *
 * @property string $clientId
 * @property string $secret
 * @property string $token
 * @property string $createToken
 * @property string $tokenExpires
 *
 */
interface PayOperationsContract
{

    /**
     * Create an authorization request.
     *
     * To collect payment at a later time, first authorize a payment using the /payment resource.
     * You can then capture the payment to complete the sale and collect payment.
     *
     * @link https://developer.paypal.com/docs/integration/direct/capture-payment/#authorize-the-payment
     * @link https://developer.paypal.com/docs/api/#authorizations
     * @param array $parameters
     * @return RestAuthorizeRequest
     */
    public function authorize(array $parameters = array());

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
     * @return RestPurchaseRequest
     */
    public function purchase(array $parameters = array());

    /**
     * Capture an authorization.
     *
     * Use this resource to capture and process a previously created authorization.
     * To use this resource, the original payment call must have the intent set to
     * authorize.
     *
     * @link https://developer.paypal.com/docs/api/#capture-an-authorization
     * @param array $parameters
     * @return RestCaptureRequest
     */
    public function capture(array $parameters = array());

    /**
     * Refund a Sale Transaction
     *
     * To get details about completed payments (sale transaction) created by a payment request
     * or to refund a direct sale transaction, PayPal provides the /sale resource and related
     * sub-resources.
     *
     * @link https://developer.paypal.com/docs/api/#sale-transactions
     * @param array $parameters
     * @return RestRefundRequest
     */
    public function refund(array $parameters = array());

    /**
     * Fetch a Sale Transaction
     *
     * To get details about completed payments (sale transaction) created by a payment request
     * or to refund a direct sale transaction, PayPal provides the /sale resource and related
     * sub-resources.
     *
     * @link https://developer.paypal.com/docs/api/#sale-transactions
     * @param array $parameters
     * @return RestFetchTransactionRequest
     */
    public function fetchTransaction(array $parameters = array());


    /**
     * Fetch a Sale Transaction
     *
     * To get details about completed payments (sale transaction) created by a payment request
     * or to refund a direct sale transaction, PayPal provides the /sale resource and related
     * sub-resources.
     *
     * @link https://developer.paypal.com/docs/classic/api/merchant/DoReferenceTransaction_API_Operation_NVP/
     * @param array $parameters
     * @return ReferenceTransactionRequest
     */
    public function referenceTransaction(array $parameters = array());
}