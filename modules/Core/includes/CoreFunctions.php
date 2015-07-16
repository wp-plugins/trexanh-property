<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Core\Formatter;
use TreXanhProperty\Core\Property;
use TreXanhProperty\Core\Order;
use TreXanhProperty\Core\PropertyForm;
use TreXanhProperty\Core\PaymentGateway\PaymentGatewayService;
use TreXanhProperty\Admin\SettingPage;

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
    $is_custom_attribute = PropertyForm::is_custom_attribute($field_name);
    if ($is_custom_attribute) {
        $input = PropertyForm::render_custom_attribute_form_element(Property::get_input_prefix() . "_" . $field_name, $post, $options);
    } else {
        $input = PropertyForm::render_form_element( $field_name, $post, $options );
    }
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

function txp_get_property_type_config( $type )
{
    $configs = get_option( SettingPage::get_config_property_settings_key() );
    $property_types_config = $configs['property_types'];
    if (!array_key_exists($type, $property_types_config)) {
        return array();
    }
    $custom_attributes_config = $configs['custom_attributes'];
    $property_type_config = $property_types_config[$type];
    $field_inputs = PropertyForm::get_fields_input();
    $prefix = Property::get_input_prefix() . "_";
    $property_type_config['attributes_data'] = array();
    foreach ( $property_type_config['attributes'] as $index => $attribute_id ) {
        if (!empty($field_inputs[$prefix.$attribute_id])) {
            $property_type_config['attributes_data'][$attribute_id] = array(
                'id' => $attribute_id,
                'label' => $field_inputs[$prefix.$attribute_id]['options']['label'],
                'type' => $field_inputs[$prefix.$attribute_id]['type'],
            );
        } else {
            foreach ($custom_attributes_config as $custom_attribute) {
                if ($custom_attribute['id'] == $prefix.$attribute_id) {
                    $property_type_config['attributes_data'][$attribute_id] = array(
                        'id' => $attribute_id,
                        'label' => $custom_attribute['title'],
                        'type' => $custom_attribute['input'],
                    );
                }
            }
        }
    }
    return $property_type_config;
}

function txp_get_default_property_type_config()
{
    return array(
        'custom_attributes' =>
        array(
            array(
                'id' => 'txp_property_pool',
                'title' => 'Pool',
                'input' => 'checkbox',
            ),
            array(
                'id' => 'txp_property_toilet',
                'title' => 'Toilet',
                'input' => 'number',
            ),
            array(
                'id' => 'txp_property_garage',
                'title' => 'Garage',
                'input' => 'number',
            ),
            array(
                'id' => 'txp_property_new_construction',
                'title' => 'New construction',
                'input' => 'checkbox',
            ),
            array(
                'id' => 'txp_property_air_conditioning',
                'title' => 'Air conditioning',
                'input' => 'checkbox',
            ),
            array(
                'id' => 'txp_property_security_system',
                'title' => 'Security system',
                'input' => 'checkbox',
            ),
            array(
                'id' => 'txp_property_bedrooms',
                'title' => 'Bedrooms',
                'input' => 'number',
            ),
            array(
                'id' => 'txp_property_bathrooms',
                'title' => 'Bathrooms',
                'input' => 'number',
            ),
            array(
                'id' => 'txp_property_ensuite',
                'title' => 'Ensuite',
                'input' => 'number',
            ),
            array(
                'id' => 'txp_property_nearby_the_beach',
                'title' => 'Nearby the beach',
                'input' => 'checkbox',
            ),
            array(
                'id' => 'txp_property_around_hospital',
                'title' => 'Around hospital',
                'input' => 'checkbox',
            ),
            array(
                'id' => 'txp_property_area',
                'title' => 'Area',
                'input' => 'number',
            ),
            array(
                'id' => 'txp_property_agent',
                'input' => 'text',
                'title' => 'Agent',
            ),
            array(
                'id' => 'txp_property_area_unit',
                'input' => 'select',
                'title' => 'Area unit',
                'options' =>
                array(
                    'Square Meter',
                    'Square Feet',
                ),
            ),
            array(
                'id' => 'txp_property_property_status',
                'input' => 'select',
                'title' => 'Property Status',
                'options' =>
                array(
                    'Current',
                    'Sold',
                ),
            ),
            array(
                'id' => 'txp_property_featured',
                'input' => 'checkbox',
                'title' => 'Featured',
            ),
            array(
                'id' => 'txp_property_rent_period',
                'input' => 'select',
                'title' => 'Rent period',
                'options' =>
                array(
                    'month',
                    'week',
                    'day',
                ),
            ),
            array(
                'id' => 'txp_property_listing_type',
                'input' => 'select',
                'title' => 'Listing type',
                'options' =>
                array(
                    'sale',
                    'lease',
                ),
            ),
        ),
        'property_types' =>
        array(
            'property' =>
            array(
                'id' => 'property',
                'name' => 'Property',
                'enabled' => false,
                'attributes' =>
                array(
                    'area',
                    'area_unit',
                    'featured',
                    'price',
                    'rent',
                    'rent_period',
                    'bedrooms',
                    'bathrooms',
                    'ensuite',
                    'toilet',
                    'garage',
                    'new_construction',
                    'air_conditioning',
                    'pool',
                    'security_system',
                    'address_postcode',
                    'address_street',
                    'address_street_number',
                    'address_city',
                    'address_state',
                    'address_country',
                    'address_coordinates',
                    'video_url',
                    'floorplan',
                    'photo_gallery',
                    'listing_type',
                    'agent',
                    'property_status',
                ),
                'groups' =>
                array(
                    array(
                        'id' => 'property_info',
                        'name' => 'Property Info',
                        'attributes' =>
                        array(
                            'area',
                            'area_unit',
                            'agent',
                            'property_status',
                            'featured',
                        ),
                    ),
                    array(
                        'id' => 'price',
                        'name' => 'Price',
                        'attributes' =>
                        array(
                            'rent',
                            'price',
                            'rent_period',
                            'listing_type',
                        ),
                    ),
                    array(
                        'id' => 'features',
                        'name' => 'Features',
                        'attributes' =>
                        array(
                            'bedrooms',
                            'bathrooms',
                            'ensuite',
                            'toilet',
                            'garage',
                            'new_construction',
                            'air_conditioning',
                            'pool',
                            'security_system',
                        ),
                    ),
                    array(
                        'id' => 'address',
                        'name' => 'Address',
                        'attributes' =>
                        array(
                            'address_postcode',
                            'address_street',
                            'address_street_number',
                            'address_city',
                            'address_state',
                            'address_country',
                            'address_coordinates',
                        ),
                    ),
                    array(
                        'id' => 'media',
                        'name' => 'Media',
                        'attributes' =>
                        array(
                            'video_url',
                            'floorplan',
                        ),
                    ),
                    array(
                        'id' => 'photo_gallery',
                        'name' => 'Photo Gallery',
                        'attributes' =>
                        array(
                            'photo_gallery',
                        ),
                    ),
                ),
            ),
            'land' =>
            array(
                'id' => 'land',
                'name' => 'Land',
                'enabled' => false,
                'attributes' =>
                array(
                    'photo_gallery',
                    'floorplan',
                    'video_url',
                    'address_postcode',
                    'address_street_number',
                    'address_street',
                    'address_state',
                    'address_city',
                    'address_country',
                    'address_coordinates',
                    'price',
                ),
                'groups' =>
                array(
                    array(
                        'id' => 'price',
                        'name' => 'Price',
                        'attributes' =>
                        array(
                            'price',
                        ),
                    ),
                    array(
                        'id' => 'media',
                        'name' => 'Media',
                        'attributes' =>
                        array(
                            'photo_gallery',
                            'video_url',
                            'floorplan',
                        ),
                    ),
                    array(
                        'id' => 'address',
                        'name' => 'Address',
                        'attributes' =>
                        array(
                            'address_postcode',
                            'address_street_number',
                            'address_street',
                            'address_state',
                            'address_city',
                            'address_country',
                            'address_coordinates',
                        ),
                    )
                ),
            ),
            'house' =>
            array(
                'id' => 'house',
                'name' => 'House',
                'enabled' => true,
                'attributes' =>
                array(
                    'price',
                    'rent',
                    'rent_period',
                    'area',
                    'area_unit',
                    'featured',
                    'bedrooms',
                    'bathrooms',
                    'ensuite',
                    'toilet',
                    'garage',
                    'listing_type',
                    'agent',
                    'property_status',
                    'new_construction',
                    'air_conditioning',
                    'pool',
                    'security_system',
                    'address_postcode',
                    'address_street',
                    'address_street_number',
                    'address_city',
                    'address_state',
                    'address_country',
                    'address_coordinates',
                    'video_url',
                    'floorplan',
                    'photo_gallery',
                ),
                'groups' =>
                array(
                    array(
                        'id' => 'overview',
                        'name' => 'Overview',
                        'attributes' =>
                        array(
                            'price',
                            'rent',
                            'rent_period',
                            'listing_type',
                            'agent',
                            'property_status',
                            'area',
                            'area_unit',
                            'bedrooms',
                            'bathrooms',
                            'ensuite',
                            'toilet',
                            'garage',
                            'featured',
                        ),
                    ),
                    array(
                        'id' => 'amenities',
                        'name' => 'Amenities',
                        'attributes' =>
                        array(
                            'new_construction',
                            'air_conditioning',
                            'pool',
                            'security_system',
                        ),
                    ),
                    array(
                        'id' => 'address',
                        'name' => 'Address',
                        'attributes' =>
                        array(
                            'address_postcode',
                            'address_street',
                            'address_street_number',
                            'address_city',
                            'address_state',
                            'address_country',
                            'address_coordinates',
                        ),
                    ),
                    array(
                        'id' => 'media',
                        'name' => 'Media',
                        'attributes' =>
                        array(
                            'video_url',
                            'floorplan',
                            'photo_gallery',
                        ),
                    ),
                ),
            ),
        ),
    );
}