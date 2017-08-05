<?php namespace HcDisat\Payment\Core\Requests;

use HcDisat\Monetary\Currency;
use HcDisat\Monetary\Money;
use HcDisat\Payment\Core\Contracts\ResponseContract;
use HcDisat\Payment\Core\Item;
use HcDisat\Payment\Responses\AbstractResponse;
use HcDisat\Payment\Responses\Response;


/**
 * Class AbstractRequest
 * @package HcDisat\Payment\Core\Requests
 *
 * @property string $username
 * @property string $password
 * @property string $signature
 * @property string $subject
 * @property string $solutionType
 * @property string $landingPage
 * @property string $headerImageUrl
 * @property string $logoImageUrl
 * @property string $borderColor
 * @property string $brandName
 * @property string $noShipping
 * @property string $allowNote
 * @property string $addressOverride
 * @property string $maxAmount
 * @property string $taxAmount
 * @property string $shippingAmount
 * @property string $handlingAmount
 * @property string $shippingDiscount
 * @property string $insuranceAmount
 * @property string $localeCode
 * @property string $customerServiceNumber
 * @property string $sellerPaypalAccountId
 * @property string $buttonSource
 * @property string $transactionReference
 */
abstract class AbstractRequest extends RequestBase
{

    public function initialize(array $parameters = [])
    {
        $api = [
            'apiLiveEndpoint' => config('payment.paypal.nvp.uri.live'),
            'apiTestEndpoint' => config('payment.paypal.nvp.uri.test'),
            'apiVersion' => config('payment.paypal.nvp.version'),
            'username' => config('payment.paypal.nvp.credentials.username'),
            'password' => config('payment.paypal.nvp.credentials.password'),
            'signature' => config('payment.paypal.nvp.credentials.signature'),
            'testMode' => config('payment.paypal.nvp.credentials.testMode')
        ];

        foreach ($api as $key => $value) {
            $parameters[$key] = $value;
        }

        return parent::initialize($parameters);
    }
    /**
     * Get the response to this request (if the request has been sent)
     *
     * @return ResponseContract
     */
    public function getResponse()
    {
        $throw = function() {
            throw new \RuntimeException('The request has not been sent yet.');
        };
        return $this->response ?? $throw();
    }

    /**
     *
     * Send the request
     *
     * @return AbstractResponse
     */
    public function send()
    {
        return is_null($data = $this->getData())
            ?: $this->sendData($data);
    }


    protected function getBaseData()
    {
        $data = [];
        $data['VERSION'] = config('payment.paypal.nvp.version');
        $data['USER'] = $this->username;
        $data['PWD'] = $this->password;
        $data['SIGNATURE'] = $this->signature;
        $data['SUBJECT'] = $this->subject;
        $bnCode = $this->buttonSource;
        if (!empty($bnCode)) {
            $data['BUTTONSOURCE'] = $bnCode;
        }

        return $data;
    }

    protected function getItemData()
    {
        $data = array();
        if ($this->items) {
            $itemAmount = new Money(0, new Currency(config('payment.currency')));
            foreach ($this->items as $n => $item) {
                $item = new Item();
                $money = new Money($item->price, new Currency(config('payment.currency')));
                $data["L_PAYMENTREQUEST_0_NAME$n"] = $item->name;
                $data["L_PAYMENTREQUEST_0_DESC$n"] = $item->description;
                $data["L_PAYMENTREQUEST_0_QTY$n"] = $item->quantity;
                $data["L_PAYMENTREQUEST_0_AMT$n"] = $money->present()->amountForHumans();
                $itemAmount = $itemAmount->add($money);
            }
            $data["PAYMENTREQUEST_0_ITEMAMT"] = $itemAmount->present()->amountForHumans();
        }

        return $data;
    }

    /**
     * @abstract
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return Response
     */
    public function sendData($data)
    {
        $url = $this->getEndpoint().'?'.http_build_query($data, '', '&');
//        $httpRequest->getCurlOptions()->set(CURLOPT_SSLVERSION, 6); // CURL_SSLVERSION_TLSv1_2 for libcurl < 7.35
        $httpResponse = $this->httpClient->get($url);

        return $this->createResponse($httpResponse->getBody());
    }

    /**
     * Get Api Uri
     * @return string
     */
    protected function getEndpoint()
    {
        return $this->testMode ? $this->apiTestEndpoint : $this->apiLiveEndpoint;
    }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }
}