<?php

use Laravel\Cashier\Invoices\DompdfInvoiceRenderer;

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe Keys
    |--------------------------------------------------------------------------
    |
    | The Stripe publishable key and secret key give you access to Stripe's
    | API. The "publishable" key is typically used when interacting with
    | Stripe.js while the "secret" key accesses private API endpoints.
    |
    */

    'key' => env('STRIPE_KEY'),

    'secret' => env('STRIPE_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Cashier Path
    |--------------------------------------------------------------------------
    |
    | This is the base URI path where Cashier's views, such as the payment
    | verification screen, will be available from. You're free to tweak
    | this path according to your preferences and application design.
    |
    */

    'path' => env('CASHIER_PATH', 'stripe'),

    /*
    |--------------------------------------------------------------------------
    | Stripe Webhooks
    |--------------------------------------------------------------------------
    |
    | Your Stripe webhook secret is used to prevent unauthorized requests to
    | your Stripe webhook handling controllers. The tolerance setting will
    | check the drift between the current time and the signed request's.
    |
    */

    'webhook' => [
        'secret'    => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | This is the default currency that will be used when generating charges
    | from your application. Of course, you are welcome to use any of the
    | various world currencies that are currently supported via Stripe.
    |
    */

    'currency' => env('CASHIER_CURRENCY', 'usd'),

    /*
    |--------------------------------------------------------------------------
    | Currency Locale
    |--------------------------------------------------------------------------
    |
    | This is the default locale in which your money values are formatted in
    | for display. To utilize other locales besides the default en locale
    | verify you have the "intl" PHP extension installed on the system.
    |
    */

    'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Payment Confirmation Notification
    |--------------------------------------------------------------------------
    |
    | If this setting is enabled, Cashier will automatically notify customers
    | whose payments require additional verification. You should listen to
    | Stripe's webhooks in order for this feature to function correctly.
    |
    */

    'payment_notification' => env('CASHIER_PAYMENT_NOTIFICATION'),

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    |
    | The following options determine how Cashier invoices are converted from
    | HTML into PDFs. You're free to change the options based on the needs
    | of your application or your preferences regarding invoice styling.
    |
    */

    'invoices' => [
        'renderer' => env('CASHIER_INVOICE_RENDERER', DompdfInvoiceRenderer::class),

        'options' => [
            // Supported: 'letter', 'legal', 'A4'
            'paper' => env('CASHIER_PAPER', 'letter'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Logger
    |--------------------------------------------------------------------------
    |
    | This setting defines which logging channel will be used by the Stripe
    | library to write log messages. You are free to specify any of your
    | logging channels listed inside the "logging" configuration file.
    |
    */

    'logger' => env('CASHIER_LOGGER'),

    'plans' => [
        [
            'name'              => 'Starter',
            'short_description' => 'This plan includes a <br /><strong>14 days FREE trial.</strong>',
            'cost_monthly'      => '€27',
            'cost_yearly'       => '€270',
            'monthly_id'        => env('START_MONTH_PLAN'),
            'yearly_id'         => env('START_YEAR_PLAN'),
            'features'          => [
                'Up to 3 customers',
                'Daily tracker app',
                '360 survey and interview',
                'Consent procedure',
                'Storage (client notes)',
            ],
            'options'           => [
                'per_seat'             => false,
                'is_free'              => false,
                'max_customers'        => 3,
                'daily_tracker_app'    => true,
                'survey_and_interview' => true,
                'consent_procedure'    => true,
                'storage_notes'        => true,
                'storage_files'        => false,
                'data_export'          => false,
                'branding'             => false,
                'unlimited'            => false,
            ],
        ],
        [
            'name'              => 'Growth',
            'short_description' => 'This plan includes a <br /><strong>14 days FREE trial.</strong>',
            'cost_monthly'      => '€57',
            'cost_yearly'       => '€570',
            'cost_coach'        => '€17',
            'cost_coach_yearly' => '€170',
            'monthly_id'        => env('GROWTH_MONTH_PLAN'),
            'yearly_id'         => env('GROWTH_YEAR_PLAN'),
            'features'          => [
                'Up to 10 customers',
                'All the features from the start plan',
                'Storage (client notes + files)',
                'Data export',
                'Home style & branding',
                '48 hours response time',
            ],
            'options'           => [
                'per_seat'             => true,
                'is_free'              => false,
                'max_customers'        => 10,
                'daily_tracker_app'    => true,
                'survey_and_interview' => true,
                'consent_procedure'    => true,
                'storage_notes'        => true,
                'storage_files'        => true,
                'data_export'          => true,
                'branding'             => true,
                'unlimited'            => false,
            ],
        ],
        [
            'name'              => 'Scale',
            'short_description' => 'This plan includes a <br /><strong>14 days FREE trial.</strong>',
            'cost_monthly'      => '€97',
            'cost_yearly'       => '€970',
            'cost_coach'        => '€17',
            'cost_coach_yearly' => '€170',
            'monthly_id'        => env('ELITE_MONTH_PLAN'),
            'yearly_id'         => env('ELITE_YEAR_PLAN'),
            'features'          => [
                'Up to 30 customers',
                'All the features from the growth plan',
                '24 hours response time',
                'Unlimited training',
            ],
            'options'           => [
                'per_seat'             => true,
                'is_free'              => false,
                'max_customers'        => 20,
                'daily_tracker_app'    => true,
                'survey_and_interview' => true,
                'consent_procedure'    => true,
                'storage_notes'        => true,
                'storage_files'        => true,
                'data_export'          => true,
                'branding'             => true,
                'unlimited'            => false,
            ],
        ],
        [
            'name'              => 'Delegate',
            'short_description' => '&nbsp;<br/>&nbsp;',
            'monthly_id'        => env('PLATINUM_MONTH_PLAN'),
            'yearly_id'         => env('PLATINUM_YEAR_PLAN'),
            'yearly_incentive'  => 'Save 2 months',
            'features'          => [
                'Unlimited customers',
                'All the features from the scale plan',
            ],
            'options'           => [
                'per_seat'             => false,
                'is_free'              => false,
                'max_customers'        => 100,
                'daily_tracker_app'    => true,
                'survey_and_interview' => true,
                'consent_procedure'    => true,
                'storage_notes'        => true,
                'storage_files'        => true,
                'data_export'          => true,
                'branding'             => true,
                'unlimited'            => true,
            ],
        ],
    ],

];
