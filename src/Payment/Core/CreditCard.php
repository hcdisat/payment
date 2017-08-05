<?php namespace HcDisat\Payment\Core;

use Carbon\Carbon;
use HcDisat\Payment\Core\Exceptions\InvalidCreditCardException;
use HcDisat\Payment\Core\Traits\CreditCardValidationTrait;
use HcDisat\Payment\Core\Traits\GetterAndSetterTrait;
use HcDisat\Payment\Core\Traits\SerializableTrait;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Credit Card class
 *
 * This class defines and abstracts all of the credit card types used
 * throughout the Omnipay system.
 *
 * Example:
 *
 * <code>
 *   // Define credit card parameters, which should look like this
 *   $parameters = [
 *       'firstName' => 'Bobby',
 *       'lastName' => 'Tables',
 *       'number' => '4444333322221111',
 *       'cvv' => '123',
 *       'expiryMonth' => '12',
 *       'expiryYear' => '2017',
 *       'email' => 'testcard@gmail.com',
 *   ];
 *
 *   // Create a credit card object
 *   $card = new CreditCard($parameters);
 * </code>
 *
 * The full list of card attributes that may be set via the parameter to
 * *new* is as follows:
 *
 * @property string $title
 * @property string $firstName
 * @property string $lastName
 * @property string $name
 * @property string $company
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property string $postcode
 * @property string $phone
 * @property string $clientIp
 * @property string $phoneExtension
 * @property string $fax
 * @property string $number
 * @property string $expiryMonth
 * @property string $expiryYear
 * @property Carbon $expiryDate
 * @property Carbon $startMonth
 * @property string $startYear
 * @property string $startDate
 * @property string $cvv
 * @property string $tracks
 * @property string $issueNumber
 * @property string $billingTitle
 * @property string $billingName
 * @property string $billingFirstName
 * @property string $billingLastName
 * @property string $billingCompany
 * @property string $billingAddress1
 * @property string $billingAddress2
 * @property string $billingCity
 * @property string $billingPostcode
 * @property string $billingState
 * @property string $billingCountry
 * @property string $billingPhone
 * @property string $billingFax
 * @property string $shippingTitle
 * @property string $shippingName
 * @property string $shippingFirstName
 * @property string $shippingLastName
 * @property string $shippingCompany
 * @property string $shippingAddress1
 * @property string $shippingAddress2
 * @property string $shippingCity
 * @property string $shippingPostcode
 * @property string $shippingState
 * @property string $shippingCountry
 * @property string $shippingPhone
 * @property string $shippingFax
 * @property string $email
 * @property string $birthday
 * @property string $gender
 *
 * If any unknown parameters are passed in, they will be ignored. No error is thrown.
 */

class CreditCard implements Arrayable, Jsonable
{
    use CreditCardValidationTrait, SerializableTrait;
    use GetterAndSetterTrait {
        parameterSetter as parameterSetterTrait;
    }

    /**
     * Exception Message
     */
    const INVALID_FIELD_MESSAGE = '%s: is not a valid credit card field.';


    /**
     * Create a new CreditCard object using the specified parameters
     *
     * @param array $parameters An array of parameters to set on the new object
     */
    public function __construct($parameters = null)
    {
        $this->initialize($parameters);
    }


    /**
     * Get Expire date
     * @param null $format
     * @return Carbon
     */
    public function getExpiryDate($format = null)
    {
        return $this->getDates(
            $this->expiryYear,
            $this->expiryMonth,
            $format
        );
    }

    /**
     * Get Expire date
     * @param null $format
     * @return Carbon
     */
    public function getStartDate($format = null)
    {
        return $this->getDates(
            $this->startYear,
            $this->startMonth,
            $format
        );
    }

    /**
     * @param $name
     * @param $value
     * @throws InvalidCreditCardException
     */
    public function parameterSetter($name, $value)
    {
        if( !in_array($name,
            config('payment.credit_card_attributes')) )
            throw new InvalidCreditCardException(sprintf(self::INVALID_FIELD_MESSAGE, $name));

        if( $name == 'name' ){
            $nameArg = explode(' ', $value);
            if( count($nameArg) >= 2 ){
                $this->parameters->put('firstName', $nameArg[0]);
                $this->parameters->put('lastName', $nameArg[1]);
            }
        }

        $this->parameterSetterTrait($name, $value);
    }


    public function getBrand()
    {
        foreach (config('payment.credit_cards') as $name => $detail) {
            if( preg_match($detail['validation_pattern'], $this->number) ){
                return $detail['name'];
            }
        }
    }

    /**
     * @param $year
     * @param $month
     * @param $format
     * @return string
     */
    private function getDates($year, $month, $format = null)
    {
        $date = Carbon::createFromDate($year, $month, null);
        return is_null($format) ? $date : $date->format($format);
    }

    /**
     * Get the last 4 digits of the card number.
     *
     * @return string
     */
    public function getNumberLastFour()
    {
        return substr($this->number, -4, 4) ?: null;
    }
}
