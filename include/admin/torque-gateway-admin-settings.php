<?php

defined( 'ABSPATH' ) || exit;

return array(
    'enabled' => array(
        'title' => __('Enable / Disable', 'torque_gateway'),
        'label' => __('Enable this payment gateway', 'torque_gateway'),
        'type' => 'checkbox',
        'default' => 'no'
    ),
    'title' => array(
        'title' => __('Title', 'torque_gateway'),
        'type' => 'text',
        'desc_tip' => __('Payment title the customer will see during the checkout process.', 'torque_gateway'),
        'default' => __('Torque Gateway', 'torque_gateway')
    ),
    'description' => array(
        'title' => __('Description', 'torque_gateway'),
        'type' => 'textarea',
        'desc_tip' => __('Payment description the customer will see during the checkout process.', 'torque_gateway'),
        'default' => __('Pay securely using Torque. You will be provided payment details after checkout.', 'torque_gateway')
    ),
    'discount' => array(
        'title' => __('Discount for using Torque', 'torque_gateway'),
        'desc_tip' => __('Provide a discount to your customers for making a private payment with Torque', 'torque_gateway'),
        'description' => __('Enter a percentage discount (i.e. 5 for 5%) or leave this empty if you do not wish to provide a discount', 'torque_gateway'),
        'type' => __('number'),
        'default' => '0'
    ),
    'valid_time' => array(
        'title' => __('Order valid time', 'torque_gateway'),
        'desc_tip' => __('Amount of time order is valid before expiring', 'torque_gateway'),
        'description' => __('Enter the number of seconds that the funds must be received in after order is placed. 3600 seconds = 1 hour', 'torque_gateway'),
        'type' => __('number'),
        'default' => '3600'
    ),
    'confirms' => array(
        'title' => __('Number of confirmations', 'torque_gateway'),
        'desc_tip' => __('Number of confirms a transaction must have to be valid', 'torque_gateway'),
        'description' => __('Enter the number of confirms that transactions must have. Enter 0 to zero-confim. Each confirm will take approximately four minutes', 'torque_gateway'),
        'type' => __('number'),
        'default' => '5'
    ),
    'confirm_type' => array(
        'title' => __('Confirmation Type', 'torque_gateway'),
        'desc_tip' => __('Select the method for confirming transactions', 'torque_gateway'),
        'description' => __('Select the method for confirming transactions', 'torque_gateway'),
        'type' => 'select',
        'options' => array(
            'viewkey'        => __('viewkey', 'torque_gateway'),
            'torque-wallet-rpc' => __('torque-wallet-rpc', 'torque_gateway')
        ),
        'default' => 'viewkey'
    ),
    'torque_address' => array(
        'title' => __('Torque Address', 'torque_gateway'),
        'label' => __('Useful for people that have not a daemon online'),
        'type' => 'text',
        'desc_tip' => __('Torque Wallet Address (TorqueL)', 'torque_gateway')
    ),
    'viewkey' => array(
        'title' => __('Secret Viewkey', 'torque_gateway'),
        'label' => __('Secret Viewkey'),
        'type' => 'text',
        'desc_tip' => __('Your secret Viewkey', 'torque_gateway')
    ),
    'daemon_host' => array(
        'title' => __('Torque wallet RPC Host/IP', 'torque_gateway'),
        'type' => 'text',
        'desc_tip' => __('This is the Daemon Host/IP to authorize the payment with', 'torque_gateway'),
        'default' => '127.0.0.1',
    ),
    'daemon_port' => array(
        'title' => __('Torque wallet RPC port', 'torque_gateway'),
        'type' => __('number'),
        'desc_tip' => __('This is the Wallet RPC port to authorize the payment with', 'torque_gateway'),
        'default' => '20188',
    ),
    'testnet' => array(
        'title' => __(' Testnet', 'torque_gateway'),
        'label' => __(' Check this if you are using testnet ', 'torque_gateway'),
        'type' => 'checkbox',
        'description' => __('Advanced usage only', 'torque_gateway'),
        'default' => 'no'
    ),
    'onion_service' => array(
        'title' => __(' SSL warnings ', 'torque_gateway'),
        'label' => __(' Check to Silence SSL warnings', 'torque_gateway'),
        'type' => 'checkbox',
        'description' => __('Check this box if you are running on an Onion Service (Suppress SSL errors)', 'torque_gateway'),
        'default' => 'no'
    ),
    'show_qr' => array(
        'title' => __('Show QR Code', 'torque_gateway'),
        'label' => __('Show QR Code', 'torque_gateway'),
        'type' => 'checkbox',
        'description' => __('Enable this to show a QR code after checkout with payment details.'),
        'default' => 'no'
    ),
    'use_torque_price' => array(
        'title' => __('Show Prices in Torque', 'torque_gateway'),
        'label' => __('Show Prices in Torque', 'torque_gateway'),
        'type' => 'checkbox',
        'description' => __('Enable this to convert ALL prices on the frontend to Torque (experimental)'),
        'default' => 'no'
    ),
    'use_torque_price_decimals' => array(
        'title' => __('Display Decimals', 'torque_gateway'),
        'type' => __('number'),
        'description' => __('Number of decimal places to display on frontend. Upon checkout exact price will be displayed.'),
        'default' => 12,
    ),
);
