<?php namespace HcDisat\Payment\Core;

use HcDisat\Payment\Core\Requests\AbstractRestRequest;
use HcDisat\Payment\Core\Requests\ReferenceTransactionRequest;
use HcDisat\Payment\Core\Requests\RestAuthorizeRequest;
use HcDisat\Payment\Core\Requests\RestCaptureRequest;
use HcDisat\Payment\Core\Requests\RestCreateCardRequest;
use HcDisat\Payment\Core\Requests\RestDeleteCardRequest;
use HcDisat\Payment\Core\Requests\RestFetchPurchaseRequest;
use HcDisat\Payment\Core\Requests\RestFetchTransactionRequest;
use HcDisat\Payment\Core\Requests\RestPurchaseRequest;
use HcDisat\Payment\Core\Requests\RestRefundRequest;
use HcDisat\Payment\Core\Requests\RestTokenRequest;
use HcDisat\Payment\Gateways\AbstractGateway;

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
class RestGateway extends AbstractGateway implements PayOperationsContract
{
    /**
     * @return string
     */
    public function getName()
    {
        return config('payment.gateways.paypal_rest.name');
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return config('payment.paypal.api.credentials');
    }
    
    /**
     * Get OAuth 2.0 access token.
     *
     * @param bool $createIfNeeded [optional] - If there is not an active token present, should we create one?
     * @return string
     */
    public function getToken($createIfNeeded = true)
    {
        if ($createIfNeeded && !$this->hasToken()) {
            $response = $this->createToken()->send();
            if ($response->isSuccessful()) {
                $data = $response->getData();
                if (isset($data['access_token'])) {
                    $this->token = $data['access_token'];
                    $this->tokenExpires = time() + $data['expires_in'];
                }
            }
        }

        return $this->parameterGetter('token');
    }

    /**
     * Create OAuth 2.0 access token request.
     *
     * @return RestTokenRequest
     */
    public function createToken()
    {
        return $this->createRequest(RestTokenRequest::class , []);
    }

    /**
     * Is there a bearer token and is it still valid?
     *
     * @return bool
     */
    public function hasToken()
    {
        $token = $this->token;
        $expires = $this->tokenExpires;
        if (!empty($expires) && !is_numeric($expires)) {
            $expires = strtotime($expires);
        }

        return !empty($token) && time() < $expires;
    }

    /**
     * Create Request
     *
     * This overrides the parent createRequest function ensuring that the OAuth
     * 2.0 access token is passed along with the request data -- unless the
     * request is a RestTokenRequest in which case no token is needed.  If no
     * token is available then a new one is created (e.g. if there has been no
     * token request or the current token has expired).
     *
     * @param string $class
     * @param array $parameters
     * @return AbstractRestRequest
     */
    public function createRequest($class, array $parameters = array())
    {
        if (!$this->hasToken() && $class != RestTokenRequest::class) {
            // This will set the internal token parameter which the parent
            // createRequest will find when it calls getParameters().
            $this->getToken(true);
        }

        $obj = new $class($this->httpClient, $this->httpRequest);

        return $obj->initialize(array_replace($this->getParameters()->toArray(), $parameters));
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
     * @return RestPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest(RestPurchaseRequest::class, $parameters);
    }

    /**
     * Fetch a purchase request.
     *
     * Use this call to get details about payments that have not completed,
     * such as payments that are created and approved, or if a payment has failed.
     *
     * @link https://developer.paypal.com/docs/api/#look-up-a-payment-resource
     * @param array $parameters
     * @return RestFetchPurchaseRequest
     */
    public function fetchPurchase(array $parameters = array())
    {
        return $this->createRequest(RestFetchPurchaseRequest::class, $parameters);
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
     * @return RestAuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest(RestAuthorizeRequest::class, $parameters);
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
     * @return RestCaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest(RestCaptureRequest::class, $parameters);
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
     * @return RestFetchTransactionRequest
     */
    public function fetchTransaction(array $parameters = array())
    {
        return $this->createRequest(RestFetchTransactionRequest::class, $parameters);
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
     * @return RestRefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest(RestRefundRequest::class, $parameters);
    }

    /**
     * Store a credit card in the vault
     *
     * You can currently use the /vault API to store credit card details
     * with PayPal instead of storing them on your own server. After storing
     * a credit card, you can then pass the credit card id instead of the
     * related credit card details to complete a payment.
     *
     * @link https://developer.paypal.com/docs/api/#store-a-credit-card
     * @param array $parameters
     * @return RestCreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest(RestCreateCardRequest::class, $parameters);
    }

    /**
     * Delete a credit card from the vault.
     *
     * Updating a card in the vault is no longer supported -- see
     * http://stackoverflow.com/questions/20858910/paypal-rest-api-update-a-stored-credit-card
     * Therefore the only way to update a card is to remove it using deleteCard and
     * then re-add it using createCard.
     *
     * @link https://developer.paypal.com/docs/api/#delete-a-stored-credit-card
     * @param array $parameters
     * @return RestDeleteCardRequest
     */
    public function deleteCard(array $parameters = array())
    {
        return $this->createRequest(RestDeleteCardRequest::class, $parameters);
    }

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
    public function referenceTransaction(array $parameters = array())
    {
        // TODO: Implement referenceTransaction() method.
    }
}