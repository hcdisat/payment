<?php

use HcDisat\Payment\Core\CreditCard;
use Tests\TestCase;

class CreditCardTest extends TestCase
{
    /**
     * @var CreditCard
     */
    private $card;

    public function setUp()
    {
        parent::setUp();
        $this->card = new CreditCard();
        $this->card->number = '4111111111111111';
        $this->card->firstName = 'Example';
        $this->card->lastName = 'Customer';
        $this->card->expiryMonth = '4';
        $this->card->cvv = '123';
        $this->card->expiryYear = (\Carbon\Carbon::now()->addYear(2)->year);
    }

    /**
     *
     */
    public function testParametersIsACollection()
    {
        $parameters = $this->card->getParameters();
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $parameters);
    }

    public function testConstructWithParameters()
    {
        $data = [
            'first_name' => 'Hector',
            'LastName' => 'Caraballo',
            'cvv' => 666
        ];

        $card = new CreditCard($data);

        $this->assertEquals($data['first_name'], $card->firstName);
        $this->assertEquals($data['LastName'], $card->lastName);
        $this->assertEquals($data['cvv'], $card->cvv);
    }

    public function testGetParameters()
    {
        $card = new CreditCard(array(
            'name' => 'John Doe',
            'number' => '1234',
            'expiryMonth' => 6,
            'expiryYear' => 2016,
        ));

        $parameters = $card->toArray();
        $this->assertEquals('John', $parameters['firstName']);
        $this->assertEquals('1234', $parameters['number']);
        $this->assertEquals('6', $parameters['expiryMonth']);
        $this->assertEquals('2016', $parameters['expiryYear']);
        $this->assertEquals('John Doe', $parameters['name']);
    }

    /**
     * @expectedException HcDisat\Payment\Core\Exceptions\InvalidCreditCardException
     * @expectedExceptionMessage Card number is invalid
     */
    public function testInvalidLuhn()
    {
        $this->card->number = '43';
        $this->card->validate();
    }

    /**
     * @expectedException \HcDisat\Payment\Core\Exceptions\InvalidCreditCardException
     * @expectedExceptionMessage Card number should have 12 to 19 digits
     */
    public function testInvalidShortNumberCard()
    {
        $this->card->number = '4440';
        $this->card->validate();
    }

    public function testGetBrand()
    {
        $this->assertEquals('visa', $this->card->getBrand());
    }

    public function testGetParametersWithCallingFunction()
    {
        $card = new CreditCard(array(
            'name' => 'John Doe',
            'number' => '1234',
            'expiryMonth' => 6,
            'expiryYear' => 2016,
            'company' => 'fooBar'
        ));

        $this->assertEquals('John Doe', $card->getName());
        $this->assertEquals('1234', $card->getNumber());
        $this->assertEquals('6', $card->getExpiryMonth());
        $this->assertEquals('2016', $card->getExpiryYear());
        $this->assertEquals('fooBar', $card->getCompany());
    }
}
