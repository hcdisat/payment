<?php

return [

    /*
     * ***************************************************************************************************************
     *  Valid Credit Card fields
     * ***************************************************************************************************************
     */
    'credit_card_attributes' => [
        'title',
        'firstName',
        'lastName',
        'name',
        'company',
        'address1',
        'clientIp',
        'address2',
        'city',
        'postcode',
        'state',
        'country',
        'phone',
        'phoneExtension',
        'fax',
        'number',
        'expiryMonth',
        'expiryYear',
        'startMonth',
        'startYear',
        'startDate',
        'cvv',
        'tracks',
        'issueNumber',
        'billingTitle',
        'billingName',
        'billingFirstName',
        'billingLastName',
        'billingCompany',
        'billingAddress1',
        'billingAddress2',
        'billingCity',
        'billingPostcode',
        'billingState',
        'billingCountry',
        'billingPhone',
        'billingFax',
        'shippingTitle',
        'shippingName',
        'shippingFirstName',
        'shippingLastName',
        'shippingCompany',
        'shippingAddress1',
        'shippingAddress2',
        'shippingCity',
        'shippingPostcode',
        'shippingState',
        'shippingCountry',
        'shippingPhone',
        'shippingFax',
        'email',
        'birthday',
        'gender',
    ],

    /*
    * ***************************************************************************************************************
    * All known/supported card brands, and a regular expression to match them.
    * The order of the card brands is important, as some of the regular expressions overlap.
    * Note: The fact that a particular card brand has been added to this array does not imply
    * that a selected gateway will support the card.
    * @link https://github.com/Shopify/active_merchant/blob/master/lib/active_merchant/billing/credit_card_methods.rb
    * ***************************************************************************************************************
    */
    'credit_cards' => [
        'visa' => [
            'name' => 'visa',
            'validation_pattern' => '/^4\d{12}(\d{3})?$/',
        ],

        'mastercard' => [
            'name' => 'mastercard',
            'validation_pattern' => '/^(5[1-5]\d{4}|677189)\d{10}$/',
        ],

        'discover' => [
            'name' => 'discover',
            'validation_pattern' => '/^(6011|65\d{2}|64[4-9]\d)\d{12}|(62\d{14})$/',
        ],

        'amex' => [
            'name' => 'amex',
            'validation_pattern' => '/^3[47]\d{13}$/',
        ],

        'diners_club' => [
            'name' => 'diners_club',
            'validation_pattern' => '/^3(0[0-5]|[68]\d)\d{11}$/',
        ],

        'jcb' => [
            'name' => 'jcb',
            'validation_pattern' => '/^35(28|29|[3-8]\d)\d{12}$/',
        ],

        'switch' => [
            'name' => 'switch',
            'validation_pattern' => '/^6759\d{12}(\d{2,3})?$/',
        ],

        'solo' => [
            'name' => 'solo',
            'validation_pattern' => '/^6767\d{12}(\d{2,3})?$/',
        ],

        'dankort' => [
            'name' => 'dankort',
            'validation_pattern' => '/^5019\d{12}$/',
        ],

        'maestro' => [
            'name' => 'maestro',
            'validation_pattern' => '/^(5[06-8]|6\d)\d{10,17}$/',
        ],

        'forbrugsforeningen' => [
            'name' => 'forbrugsforeningen',
            'validation_pattern' => '/^600722\d{10}$/',
        ],

        'laser' => [
            'name' => 'laser',
            'validation_pattern' => '/^(6304|6706|6709|6771(?!89))\d{8}(\d{4}|\d{6,7})?$/',
        ],
    ],

    'currency' => 'USD',

    /*
     * ****************************************************************************************************************
     * Paypal Api details
     * ****************************************************************************************************************
     */
    'paypal' => [
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
        
        'request_data_skeleton' => [
            'intent' => 'authorize',
            'payer' => [
                'payment_method' => 'credit_card',
                'funding_instruments' => []
            ],
            'transactions' => [
                [
                    'description' => '',
                    'amount' => [
                        'total' => '',
                        'currency' => '',
                    ],
                ]
            ],
            'experience_profile_id' => ''
        ]
    ],

    'gateways' => [
        'paypal_pro' => [
            'name' => 'PayPal Pro',
            'class' => \CVA\Payment\Core\ProGateway::class
        ],
        'paypal_rest' => [
            'name' => 'PayPal REST',
            'class' => \CVA\Payment\Core\RestGateway::class
        ],

    ]
];