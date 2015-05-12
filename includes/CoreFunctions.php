<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Core\Formatter;
use TreXanhProperty\Core\Property;

/**
 * 
 * @param string|float|int $number
 * @param string $currency_code
 * @param string $symbol_pos left|right
 * @return string
 */
function txp_currency( $number, $currency_code = '', $symbol_pos = '' )
{
    return Formatter::currency( $number, $currency_code, $symbol_pos );
}

function txp_get_property( $property )
{
    return new Property( $property );
}

function txp_get_ip()
{
    $client_ip_keys = array(
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR',
    );
    
    foreach ( $client_ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return null;
}