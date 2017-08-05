# Payments

Omnipay Paypal gateway folk. Optimized for Laravel 5.2 and PHP 7

## Getting Started

These instructions will get you a copy of the project up and running on your
local machine for development and testing purposes.

### Installing

Then, add the actual package to the required object

```
"hcdisat/payment": "dev-master"
```

After that, update composer.

```
composer update
```
At this point the package has been installed, lets move to configuration process.

### Configuration

First lets register the service provider. add the next line to the providers array in _**config/app.php**_
```
\HcDisat\Payment\PaymentServiceProvider::class,

```

Also we need publish the monetary service provider to be able to format currencies
correctly
```
\HcDisat\Monetary\MonetaryServiceProvider::class,
```
Then publish the configuration file:

```
php artisan vendor:publish
```
Done. Lets move the configuration process.

## Configuration
Lets first setup the monetary package, this is very simple, just setup your default
locale and money format, note this are options for NumberFormatter class. You get more info about this class at
[php-intl](http://php.net/manual/es/class.numberformatter.php) official documentation.

At this point you should have this file **_config/currency.php_**. you can set
these entries according to your needs.

```
  'default-locale' => 'en_US',

  'formatter-format' => '#,##0.00, -#,##0.00',
```

defaults works very fine.

Before you can start to use this package, you need to setup your paypal credentials
in the ***config/payment.php*** file. if you plan to use the rest api then fill the
paypal.api_rest section:

```
 'api_rest' => [
    'uri' => [
        'live' => 'https://api.paypal.com',
        'test' => 'https://api.sandbox.paypal.com',
    ],
    'version' => 'v1',
    'credentials' => [
       'clientId'     => '',
       'secret'       => '',
       'token'        => '',
       'testMode'     => false,
    ],
 ],
```
You must fill the credentials array.

If you plan to use nvp instead, fill the paypal.nvp section:

```
 'nvp' => [
    'uri' => [
        'live' => 'https://api-3t.paypal.com/nvp',
        'test' => 'https://api-3t.sandbox.paypal.com/nvp',
    ],
    'version'  => '119.0',
    'credentials' => [
        'username' => '',
        'password' => '',
        'signature' => '',
        'testMode' => false,
    ]
],
```
Done.

## Using the code.

```
use \HcDisat\Payment\Payment;

$options = [
    'amount' => '10.00',
    'card' => new HcDisat\Payments\Core\CreditCard([
        'firstName' => 'Example',
        'lastName' => 'User',
        'number' => '4032031662872158',
        'expiryMonth' => '12',
        'expiryYear' => '2016',
        'cvv' => '123',
    ]),
];

$gateway = Payment::create('PayPal Pro');
$response = $gateway->purchase($options)->send();

if( $response->isSuccessful() ) {
    // success logic
}
```

Gateways support the following methods:
* authorize
* purchase
* capture
* refund
* fetchTransaction
* referenceTransaction

They can be used with the same signature, they accept an array
with the request parameters as an argument.