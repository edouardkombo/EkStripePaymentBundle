Ek Stripe Payment Bundle
========================

About
-----

EKSTripePaymentBundle is the most advanced stripe php api for symfony2.
It helps you:
- Make instant payments via Stripe form
- Create a customer
- Create a plan
- Subscribe to a plan and cancel subscribtion
- never credit card numbers inside your server

This bundle is build on CABIN design pattern.


Requirements
------------

Require PHP version 5.3 or greater.


Installation
------------

Register the bundle in your composer.json

    {
        "require": {
            "edouardkombo/ek-stripe-payment-bundle": "dev-master"
        }
    }

Now, install the vendor

    php composer.phar install


Register MultiStepFormsBundle namespace in your app/appKernel.php

    new EdouardKombo\EkStripePaymentBundle\EkStripePaymentBundle(),


Documentation
-------------

Copy config parameters in app/config/config.yml:

    ek_stripe_payment:   
        current_environment:    'test'  #test or live allowed
        default_currency:       'EUR'    
        api_url:                'https://api.stripe.com/v1'
        charges_suburl:         '/charges'
        customers_suburl:       '/customers'
        plans_suburl:           '/plans'
        subscriptions_suburl:   '/subscriptions'
        invoices_suburl:        '/invoices'    
        api_checkout_url:       'https://checkout.stripe.com/checkout.js'
        api_version:            '2014-01-31'
        test:
            secret_api:         'sk_test_xxxxxxxxxxxxxxxxxxxxxxxx'
            publishable_api:    'pk_test_xxxxxxxxxxxxxxxxxxxxxxxx'
        live:
            secret_api:         'sk_live_xxxxxxxxxxxxxxxxxxxxxxxx'
            publishable_api:    'pk_live_xxxxxxxxxxxxxxxxxxxxxxxx'

And, copy route in app/config/routing.yml

    ek_stripe_payment:
        resource: "@EkStripePaymentBundle/Resources/config/routing.yml"
        prefix:   /{_locale}/payment


To see a concrete live example, these are some links you can test the bundle with:

    http://localhost/app_dev.php/{your_locale}/payment/card #List of payment buttons for instant payment and subscriptions
    http://localhost/app_dev.php/{your_locale}/payment/plan #Create a plan

All you have to is taking a look at: Controller/StripeController.php file.


NB: This bundle is evolving quickly, but you can now use it in production.


Contributing
------------

Each project has its own specifities. Feel free to help me involve this bundle with your needs.
If you want to help me improve this bundle, please make sure it conforms to the PSR coding standard. The easiest way to contribute is to work on a checkout of the repository, or your own fork, rather than an installed version.

Issues
------

Bug reports and feature requests can be submitted on the [Github issues tracker](https://github.com/edouardkombo/EkStripePaymentBundle/issues).

For further informations, contact me directly at edouard.kombo@gmail.com.

