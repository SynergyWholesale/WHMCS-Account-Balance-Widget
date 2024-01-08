<?php

define('SW_ACCOUNT_BALANCE_WIDGET_VERSION', '{{VERSION}}');

function synergywholesale_balance_config()
{
    return [
        'name' => 'Synergy Wholesale Account Balance',
        'description' => 'View your Synergy Wholesale account balance via WHMCS',
        'version' > SW_ACCOUNT_BALANCE_WIDGET_VERSION,
        'author' => 'Synergy Wholesale',
        'fields' => [ 
            'api_key' => ['Type' => 'text', 'Size' => '60', 'FriendlyName' => 'API Key'],
            'reseller_id' => ['Type' => 'text', 'Size' => '60', 'FriendlyName' => 'Reseller ID']
        ]
    ];
}