<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Core\Formatter;
use TreXanhProperty\Core\Property;
use TreXanhProperty\Core\Order;
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

/**
 * 
 * @param \WP_Post|int $property
 * @return Property
 */
function txp_get_property( $property )
{
    return new Property( $property );
}

/**
 * 
 * @param \WP_Post|int  $order
 * @return Order
 */
function txp_get_order( $order )
{
    return new Order( $order );
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

function txp_get_property_location_string( $property, $separator = ',' )
{
    $location_string = "";
    if ( $property->address_postcode ) {
        $location_string .= $property->address_postcode  . $separator . ' ';
    }
    if ( $property->address_street_number ) {
        $location_string .= $property->address_street_number  . ' ';
    }
    if ( $property->address_street ) {
        $location_string .= $property->address_street  . $separator . ' ';
    }
    if ( $property->address_city ) {
        $location_string .= $property->address_city  . $separator . ' ';
    }
    if ( $property->address_state ) {
        $location_string .= $property->address_state  . $separator . ' ';
    }
    if ( $property->address_country ) {
        $location_string .= $property->address_country;
    }
    if (
            ! $property->address_postcode &&
            ! $property->address_street_number &&
            ! $property->address_street &&
            ! $property->address_city &&
            ! $property->address_state &&
            ! $property->address_country
    ) {
        $location_string = "";
    }
    return $location_string;
}

/**
 * return theme name in lowercase with spaces replaced with dashes
 * @return type
 */
function txp_get_theme_name()
{
    return strtolower( str_replace( ' ', '_', get_current_theme() ) );
}