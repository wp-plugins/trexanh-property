<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Core\Formatter;
use TreXanhProperty\Core\Property;
use TreXanhProperty\Core\PropertyForm;
use TreXanhProperty\Core\PaymentGateway\PaymentGatewayService;

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

function txp_get_available_payment_gateways()
{
//    $gateway_service = new PaymentGatewayService();
    $gateway_service = PaymentGatewayService::getInstance();
    return $gateway_service->get_available_gateway();
}

/**
 * 
 * @param type $gateway_id
 * @return \TreXanhProperty\Core\PaymentGateway\PaymentGatewayInterface
 */
function get_payment_gateway( $gateway_id )
{
    $gateway_service = PaymentGatewayService::getInstance();
    return $gateway_service->get( $gateway_id );
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

function txp_render_property_form_element( $field_name, $post = null, $options = array() )
{
    $input = PropertyForm::render_form_element( $field_name, $post, $options );
    echo $input['html'];
}