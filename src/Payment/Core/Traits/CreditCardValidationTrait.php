<?php namespace HcDisat\Payment\Core\Traits;

use Carbon\Carbon;
use HcDisat\Payment\Core\Exceptions\InvalidCreditCardException;

trait CreditCardValidationTrait
{
    /**
     * Validate this credit card. If the card is invalid, InvalidCreditCardException is thrown.
     *
     * This method is called internally by gateways to avoid wasting time with an API call
     * when the credit card is clearly invalid.
     *
     * Generally if you want to validate the credit card yourself with custom error
     * messages, you should use your framework's validation library, not this method.
     *
     * @throws InvalidCreditCardException
     * @return void
     */
    public function validate()
    {
        foreach (array('number', 'expiryMonth', 'expiryYear') as $key) {
            if (!$this->$key) {
                throw new InvalidCreditCardException("The $key parameter is required");
            }
        }

        if ($this->getExpiryDate()->lt(Carbon::now()) ) {
            throw new InvalidCreditCardException('Card has expired');
        }

        if (!$this->validateLuhn($this->number)) {
            throw new InvalidCreditCardException('Card number is invalid');
        }

        if (!is_null($this->number) && !preg_match('/^\d{12,19}$/i', $this->number)) {
            throw new InvalidCreditCardException('Card number should have 12 to 19 digits');
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        try {
            $this->validate();
            return true;
        }
        catch(\Exception $ex) {
            return false;
        }
    }

    /**
     * @param $number
     * @return bool
     */
    private function validateLuhn($number)
    {
        $str = '';
        foreach (array_reverse(str_split($number)) as $i => $c) {
            $str .= $i % 2 ? $c * 2 : $c;
        }

        return array_sum(str_split($str)) % 10 === 0;
    }
}