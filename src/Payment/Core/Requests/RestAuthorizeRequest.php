<?php namespace HcDisat\Payment\Core\Requests;

use HcDisat\Monetary\Currency;
use HcDisat\Monetary\Money;
use HcDisat\Payment\Core\ItemBag;
use HcDisat\Payment\Core\Responses\RestAuthorizeResponse;

/**
 * Class AuthorizeRequest
 * @package HcDisat\Payment\Core
 * @property string $experienceProfileId
 */
class RestAuthorizeRequest extends AbstractRestRequest
{

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $data = config('payment.paypal.request_data_skeleton');
        $data['transactions'][0]['description'] = $this->getDescription();
        $data['transactions'][0]['amount']['total'] = $this->amount;
        $data['transactions'][0]['amount']['currency'] = $this->currency;
        $data['experience_profile_id'] = $this->experienceProfileId;

        if( $this->items ) {
            $itemsList = [];
            foreach ($this->items as $item) {
                $amount = new Money($item->price, new Currency(config('payment.currency')));
                $itemsList[] = [
                    'name' => $item->name,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'price' => $amount->present()->amountForHumans(),
                    'currency' => $amount->currency()->isoCode()
                ];
            }
            $data['transactions'][0]['item_list']['items'] = $itemsList;
        }

        if( $this->cardReference ) {
            $this->validate('amount');

            $data['payer']['funding_instruments'][] = [
                'credit_card_token' => [
                    'credit_card_id' => $this->cardReference,
                ],
            ];

        } elseif ( $this->card ) {
            $this->validate('amount', 'card');
            $this->card->validate();


            $data['payer']['funding_instruments'][] = [
                'credit_card' => [
                    'number' => $this->card->number,
                    'type' => $this->card->getBrand(),
                    'expire_month' => $this->card->expiryMonth,
                    'expire_year' => $this->card->expiryYear,
                    'cvv2' => $this->card->cvv,
                    'first_name' => $this->card->firstName,
                    'last_name' => $this->card->lastName,
                    'billing_address' => [
                        'line1' => $this->card->billingAddress1,
                        'line2' => $this->card->billingAddress2 ?? null,
                        'city' => $this->card->billingCity,
                        'state' => $this->card->billingState,
                        'postal_code' => $this->card->billingPostcode,
                        'country_code' => strtoupper($this->card->billingCountry),
                    ]
                ]
            ];
        } else {

            $this->validate('amount', 'returnUrl', 'cancelUrl');

            unset($data['payer']['funding_instruments']);

            $data['payer']['payment_method'] = 'paypal';
            $data['redirect_urls'] = [
                'return_url' => $this->returnUrl,
                'cancel_url' => $this->cancelUrl,
            ];
        }

        return $data;
    }

    /**
     * Get transaction description.
     *
     * The REST API does not currently have support for passing an invoice number
     * or transaction ID.
     *
     * @return string
     */
    public function getDescription()
    {
        $id = $this->transactionId;
        $desc = $this->description;

        if (empty($id)) {
            return $desc;
        } elseif (empty($desc)) {
            return $id;
        } else {
            return "$id : $desc";
        }
    }

    /**
     * Set the items in this order
     *
     * @param ItemBag|array $items An array of items in this order
     * @return RestAuthorizeRequest
     */
    public function setItems($items)
    {
        if ($items && !$items instanceof ItemBag) {
            $items = new ItemBag($items);
        }

        return $this->parameterSetter('items', $items);
    }

    /**
     * Get transaction endpoint.
     *
     * Authorization of payments is done using the /payment resource.
     *
     * @return string
     */
    protected function getEndpoint()
    {
        return parent::getEndpoint() . '/payments/payment';
    }

    /**
     * @param $data
     * @param $statusCode
     * @return RestAuthorizeResponse
     */
    protected function createResponse($data, $statusCode)
    {
        return $this->response = new RestAuthorizeResponse($this, $data, $statusCode);
    }
}